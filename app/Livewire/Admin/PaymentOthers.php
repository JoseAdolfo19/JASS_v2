<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\ExtraordinaryPayment;
use App\Models\ExtraordinaryPaymentType;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PaymentOthers extends Component
{

    // =========================================================================
    // PROPIEDADES — BÚSQUEDA Y SOCIO
    // =========================================================================

    public string $search       = '';
    public $asociado_id         = null;
    public $asociadoSeleccionado = null;
    public $payments            = [];

    // =========================================================================
    // PROPIEDADES — CUOTAS DISPONIBLES
    // =========================================================================

    /** IDs de cuotas extraordinarias seleccionadas para cobrar */
    public array $cuotasSeleccionadas = [];

    /** Total a cobrar */
    public float $totalFinal = 0;

    // =========================================================================
    // PROPIEDADES — GESTIÓN DE TIPOS (CRUD)
    // =========================================================================

    public bool   $mostrarFormulario = false;
    public ?int   $editandoId        = null;
    public string $formNombre        = '';
    public string $formDescripcion   = '';
    public string $formMonto         = '';
    public string $formFecha         = '';

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->formFecha = now()->toDateString();
    }

    // =========================================================================
    // SELECCIÓN DE SOCIO
    // =========================================================================

    public function seleccionarSocio(int $id): void
    {
        $this->asociado_id          = $id;
        $this->search               = '';
        $this->cuotasSeleccionadas  = [];
        $this->totalFinal           = 0;
        $this->asociadoSeleccionado = Associate::find($id);

        $this->payments = Payment::where('associate_id', $id)
            ->where('type', 'extraordinario')
            ->latest()
            ->get();
    }

    public function limpiarSocio(): void
    {
        $this->reset([
            'asociado_id', 'asociadoSeleccionado', 'cuotasSeleccionadas',
            'totalFinal', 'payments',
        ]);
    }

    // =========================================================================
    // TOGGLE DE CUOTAS
    // =========================================================================

    public function toggleCuota(int $tipoId): void
    {
        if (in_array($tipoId, $this->cuotasSeleccionadas)) {
            $this->cuotasSeleccionadas = array_values(
                array_diff($this->cuotasSeleccionadas, [$tipoId])
            );
        } else {
            $this->cuotasSeleccionadas[] = $tipoId;
        }

        $this->recalcularTotal();
    }

    private function recalcularTotal(): void
    {
        if (empty($this->cuotasSeleccionadas)) {
            $this->totalFinal = 0;
            return;
        }

        $this->totalFinal = ExtraordinaryPaymentType::whereIn('id', $this->cuotasSeleccionadas)
            ->sum('amount');
    }

    // =========================================================================
    // CONFIRMAR PAGO
    // =========================================================================

    public function confirmarPago(): mixed
    {
        if (!$this->asociado_id || empty($this->cuotasSeleccionadas)) {
            return null;
        }

        $tipos = ExtraordinaryPaymentType::whereIn('id', $this->cuotasSeleccionadas)->get();

        // Generar número correlativo global
        $ultimo        = Payment::max('invoice_number');
        $nuevoNro      = $ultimo ? intval($ultimo) + 1 : 1;
        $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

        $conceptos = $tipos->pluck('name')->map(fn($n) => strtoupper($n))->implode(' + ');

        $payment = Payment::create([
            'invoice_number'   => $invoiceNumber,
            'associate_id'     => $this->asociado_id,
            'amount'           => $this->totalFinal,
            'type'             => 'extraordinario',
            'concept'          => $conceptos,
            'months_paid'      => [],
            'late_fee_applied' => 0,
            'fine_amount'      => 0,
        ]);

        // Registrar cada cuota pagada en la tabla pivote
        foreach ($tipos as $tipo) {
            ExtraordinaryPayment::create([
                'extraordinary_payment_type_id' => $tipo->id,
                'associate_id'                  => $this->asociado_id,
                'payment_id'                    => $payment->id,
                'amount_paid'                   => $tipo->amount,
            ]);
        }

        // Recargar historial
        $this->payments = Payment::where('associate_id', $this->asociado_id)
            ->where('type', 'extraordinario')
            ->latest()
            ->get();

        $this->cuotasSeleccionadas = [];
        $this->totalFinal          = 0;

        return $this->generarReciboPDF($payment, $tipos);
    }

    // =========================================================================
    // PDF
    // =========================================================================

    public function imprimirPago(int $paymentId): mixed
    {
        $payment = Payment::find($paymentId);
        if (!$payment || $payment->associate_id !== $this->asociado_id) {
            return null;
        }

        $tipos = ExtraordinaryPayment::where('payment_id', $paymentId)
            ->with('type')
            ->get()
            ->map(fn($ep) => $ep->type);

        return $this->generarReciboPDF($payment, $tipos);
    }

    private function generarReciboPDF(Payment $payment, $tipos): mixed
    {
        $asociado = Associate::with('sector')->find($payment->associate_id);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        $items = $tipos->map(fn($t) => [
            'nombre'      => strtoupper($t->name),
            'descripcion' => $t->description,
            'monto'       => $t->amount,
        ]);

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'items'         => $items,
            'total'         => $payment->amount,
            'fecha_emision' => now()->format('d/m/Y H:i'),
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo-extraordinario', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper([0, 0, 226.77, 700], 'portrait');
            echo $pdf->output();
        }, 'recibo-ext-' . $payment->invoice_number . '.pdf');
    }

    // =========================================================================
    // CRUD DE TIPOS DE CUOTA EXTRAORDINARIA
    // =========================================================================

    public function abrirFormulario(?int $id = null): void
    {
        $this->editandoId = $id;

        if ($id) {
            $tipo                = ExtraordinaryPaymentType::find($id);
            $this->formNombre    = $tipo->name;
            $this->formDescripcion = $tipo->description ?? '';
            $this->formMonto     = (string) $tipo->amount;
            $this->formFecha     = $tipo->decided_at?->toDateString() ?? now()->toDateString();
        } else {
            $this->formNombre    = '';
            $this->formDescripcion = '';
            $this->formMonto     = '';
            $this->formFecha     = now()->toDateString();
        }

        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
        $this->editandoId        = null;
    }

    public function guardarTipo(): void
    {
        $this->validate([
            'formNombre' => 'required|string|max:120',
            'formMonto'  => 'required|numeric|min:0.01',
            'formFecha'  => 'nullable|date',
        ]);

        $datos = [
            'name'        => trim($this->formNombre),
            'description' => trim($this->formDescripcion) ?: null,
            'amount'      => (float) $this->formMonto,
            'decided_at'  => $this->formFecha ?: null,
        ];

        if ($this->editandoId) {
            ExtraordinaryPaymentType::find($this->editandoId)?->update($datos);
        } else {
            ExtraordinaryPaymentType::create(array_merge($datos, ['active' => true]));
        }

        $this->cerrarFormulario();
    }

    public function toggleActivo(int $id): void
    {
        $tipo = ExtraordinaryPaymentType::find($id);
        if ($tipo) {
            $tipo->update(['active' => !$tipo->active]);
        }
    }

    public function eliminarTipo(int $id): void
    {
        ExtraordinaryPaymentType::find($id)?->delete();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $associates = [];

        if (strlen($this->search) >= 2) {
            $associates = Associate::where('status', 'activo')
                ->where(fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%')
                )
                ->orderBy('last_name')
                ->limit(10)
                ->get();
        }

        // Cuotas activas con indicador de si el socio seleccionado ya pagó
        $tiposCuota = ExtraordinaryPaymentType::orderBy('decided_at', 'desc')
            ->get()
            ->map(function ($tipo) {
                $tipo->ya_pago = $this->asociado_id
                    ? $tipo->isPaidBy($this->asociado_id)
                    : false;
                return $tipo;
            });

        return view('livewire.admin.payment-others', [
            'associates' => $associates,
            'tiposCuota' => $tiposCuota,
        ]);
    }
}
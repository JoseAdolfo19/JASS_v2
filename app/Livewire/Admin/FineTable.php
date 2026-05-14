<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\EventAttendance;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;

class FineTable extends Component
{
    protected $layout = 'layouts.app';

    private const FINE_AMOUNT = 60.00;

    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public $asociado_id;
    public string $search        = '';
    public $payments             = [];
    public float $finesPendientes = 0;
    public int   $cantidadMultas  = 0;
    public float $totalFinal      = 0;
    public $multasDetalle         = [];

    // =========================================================================
    // SELECCIÓN DE SOCIO
    // =========================================================================

    public function seleccionarSocio(int $id): void
    {
        $this->asociado_id = $id;
        $this->search      = '';
        $this->cargarMultas($id);
    }

    public function updatedAsociadoId($value): void
    {
        $this->reset(['multasDetalle', 'finesPendientes', 'cantidadMultas', 'totalFinal', 'payments']);
        if (!$value) return;
        $this->cargarMultas($value);
    }

    private function cargarMultas(int $asociadoId): void
    {
        $this->reset(['multasDetalle', 'finesPendientes', 'cantidadMultas', 'totalFinal']);

        $asociado = Associate::find($asociadoId);
        if (!$asociado) return;

        // Historial de pagos de tipo 'falta'
        $this->payments = Payment::where('associate_id', $asociadoId)
            ->where('type', 'falta')
            ->latest()
            ->get();

        // Faltas pendientes de cobro
        $faltas = EventAttendance::where('associate_id', $asociadoId)
            ->where('status', 'ausente')
            ->where('fine_paid', false)
            ->whereHas('event', fn($q) => $q->where('lista_cerrada', true))
            ->with('event')
            ->get();

        $this->cantidadMultas  = $faltas->count();
        $this->finesPendientes = $this->cantidadMultas * self::FINE_AMOUNT;
        $this->totalFinal      = $this->finesPendientes;

        // Detalle de cada falta para mostrar en la vista
        $this->multasDetalle = $faltas->map(fn($fa) => [
            'evento'  => $fa->event->nombre ?? 'Evento',
            'fecha'   => $fa->event->fecha
                ? Carbon::parse($fa->event->fecha)->format('d/m/Y')
                : '—',
            'monto'   => self::FINE_AMOUNT,
        ])->toArray();
    }

    // =========================================================================
    // CONFIRMAR COBRO DE MULTAS
    // =========================================================================

    public function confirmarPago(): mixed
    {
        if (!$this->asociado_id || $this->finesPendientes <= 0) {
            return null;
        }

        // Número correlativo compartido con pagos de cuota
        $ultimo        = Payment::max('invoice_number');
        $nuevoNro      = $ultimo ? intval($ultimo) + 1 : 1;
        $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

        $concept = 'MULTAS POR FALTA: ' . $this->cantidadMultas . ' x S/ ' . number_format(self::FINE_AMOUNT, 2);

        $payment = Payment::create([
            'invoice_number'   => $invoiceNumber,
            'associate_id'     => $this->asociado_id,
            'amount'           => $this->totalFinal,
            'type'             => 'falta',
            'concept'          => $concept,
            'months_paid'      => [],
            'late_fee_applied' => 0,
            'fine_amount'      => $this->finesPendientes,
        ]);

        // Marcar faltas como pagadas
        EventAttendance::where('associate_id', $this->asociado_id)
            ->where('status', 'ausente')
            ->where('fine_paid', false)
            ->whereHas('event', fn($q) => $q->where('lista_cerrada', true))
            ->update(['fine_paid' => true]);

        return $this->generarReciboPDF($payment);
    }

    // =========================================================================
    // PDF
    // =========================================================================

    private function generarReciboPDF(Payment $payment): mixed
    {
        $asociado = Associate::with('sector')->find($payment->associate_id);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => collect([]),
            'subtotal'      => 0,
            'mora'          => 0,
            'fine'          => (float) $payment->fine_amount,
            'total'         => (float) $payment->amount,
            'fecha_emision' => now()->format('d/m/Y H:i'),
            'multasDetalle' => $this->multasDetalle,
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo-falta', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper([0, 0, 226.77, 600], 'portrait');
            echo $pdf->output();
        }, 'recibo-' . $payment->invoice_number . '.pdf');
    }

    public function imprimirPago(int $paymentId): mixed
    {
        $payment = Payment::find($paymentId);
        if (!$payment || $payment->associate_id !== $this->asociado_id) {
            return null;
        }

        $asociado = Associate::with('sector')->find($payment->associate_id);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => collect([]),
            'subtotal'      => 0,
            'mora'          => 0,
            'fine'          => (float) ($payment->fine_amount ?? 0),
            'total'         => (float) $payment->amount,
            'fecha_emision' => now()->format('d/m/Y H:i'),
            'multasDetalle' => [],
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo-falta', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper([0, 0, 226.77, 600], 'portrait');
            echo $pdf->output();
        }, 'recibo-' . $payment->invoice_number . '.pdf');
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

        return view('livewire.admin.fine-table', [
            'associates' => $associates,
        ]);
    }
}
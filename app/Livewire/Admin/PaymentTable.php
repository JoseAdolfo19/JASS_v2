<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;

class PaymentTable extends Component
{
    protected $layout = 'layouts.app';

    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public $asociado_id;
    public string $search           = '';
    public $resumenDeuda            = null;
    public array $mesesSeleccionados = [];
    public float $montoCobrar       = 10;
    public bool  $aplicarMora       = true;
    public float $totalFinal        = 0;

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        // Leer cuota desde Settings para no tener valores fijos
        $this->montoCobrar = (float) Setting::get('cuota_mensual', 10);
    }

    // =========================================================================
    // SELECCIÓN DE SOCIO
    // =========================================================================

    public function seleccionarSocio(int $id): void
    {
        $this->asociado_id = $id;
        $this->search      = '';
        $this->cargarDeuda($id);
    }

    public function updatedAsociadoId($value): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal']);
        if (!$value) return;
        $this->cargarDeuda($value);
    }

    private function cargarDeuda(int $asociadoId): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal']);

        $asociado = Associate::find($asociadoId);
        if (!$asociado) return;

        $fechaInicio = $asociado->entry_date
            ? Carbon::parse($asociado->entry_date)->startOfMonth()
            : Carbon::parse($asociado->created_at)->startOfMonth();

        $mesActual = Carbon::now()->startOfMonth();

        // Meses ya pagados
        $mesesPagados = Payment::where('associate_id', $asociadoId)
            ->get()
            ->pluck('months_paid')
            ->flatten()
            ->toArray();

        // Construir lista de meses pendientes
        $listaDeuda    = [];
        $deudaAgrupada = [];
        $tempFecha     = $fechaInicio->copy();

        while ($tempFecha->lessThanOrEqualTo($mesActual)) {
            $mesAnio = $tempFecha->format('Y-m');

            if (!in_array($mesAnio, $mesesPagados)) {
                $listaDeuda[]    = $mesAnio;
                $deudaAgrupada[] = [
                    'etiqueta' => strtoupper(Carbon::parse($mesAnio)->translatedFormat('F Y')),
                    'meses'    => [$mesAnio],
                ];
            }

            $tempFecha->addMonth();
        }

        $this->resumenDeuda      = ['items' => $deudaAgrupada, 'mora_calculada' => 0];
        $this->mesesSeleccionados = $listaDeuda;
        $this->actualizarTotal();
    }

    // =========================================================================
    // CÁLCULO DE TOTALES
    // =========================================================================

    public function actualizarTotal(): void
    {
        if (!$this->resumenDeuda) return;

        $cantidad    = count($this->mesesSeleccionados);
        $subtotal    = $cantidad * $this->montoCobrar;

        $montoMora   = (float) Setting::get('mora_monto', 60);
        $mesesMora   = (int)   Setting::get('mora_meses', 3);
        $bloquesMora = $mesesMora > 0 ? floor($cantidad / $mesesMora) : 0;
        $mora        = $bloquesMora * $montoMora;

        $this->resumenDeuda['mora_calculada'] = $mora;
        $this->totalFinal = $subtotal + ($this->aplicarMora ? $mora : 0);
    }

    public function updatedAplicarMora(): void  { $this->actualizarTotal(); }
    public function updatedMontoCobrar(): void  { $this->actualizarTotal(); }

    public function toggleMes(string $mes): void
    {
        if (in_array($mes, $this->mesesSeleccionados)) {
            $this->mesesSeleccionados = array_values(array_diff($this->mesesSeleccionados, [$mes]));
        } else {
            $this->mesesSeleccionados[] = $mes;
        }
        $this->actualizarTotal();
    }

    // =========================================================================
    // CONFIRMAR PAGO + GENERAR RECIBO PDF
    // =========================================================================

    public function confirmarPago(): mixed
    {
        if (empty($this->mesesSeleccionados) || !$this->asociado_id) return null;

        // Generar número correlativo
        $ultimo   = Payment::max('invoice_number');
        $nuevoNro = $ultimo ? intval($ultimo) + 1 : 1;
        $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

        $moraAplicada = $this->aplicarMora
            ? ($this->resumenDeuda['mora_calculada'] ?? 0)
            : 0;

        // Guardar el pago
        $payment = Payment::create([
            'invoice_number'   => $invoiceNumber,
            'associate_id'     => $this->asociado_id,
            'amount'           => $this->totalFinal,
            'type'             => 'cuota',
            'concept'          => 'PAGO DE MESES: ' . implode(', ', $this->mesesSeleccionados),
            'months_paid'      => $this->mesesSeleccionados,
            'late_fee_applied' => $moraAplicada,
        ]);

        // Generar y descargar el recibo PDF
        return $this->generarReciboPDF($payment);
    }

    private function generarReciboPDF(Payment $payment): mixed
    {
        $asociado = Associate::with('sector')->find($this->asociado_id);

        // Datos de la JASS desde Settings
        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        // Construir desglose de meses para el recibo
        $meses = collect($this->mesesSeleccionados)->map(function ($mes) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $this->montoCobrar,
            ];
        });

        $subtotal     = count($this->mesesSeleccionados) * $this->montoCobrar;
        $moraAplicada = $this->aplicarMora ? ($this->resumenDeuda['mora_calculada'] ?? 0) : 0;

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => $meses,
            'subtotal'      => $subtotal,
            'mora'          => $moraAplicada,
            'total'         => $this->totalFinal,
            'fecha_emision' => now()->format('d/m/Y H:i'),
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            // Tamaño ticket 80mm
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

        return view('livewire.admin.payment-table', [
            'associates' => $associates,
        ]);
    }
}
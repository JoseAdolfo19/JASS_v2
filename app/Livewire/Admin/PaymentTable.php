<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\EventAttendance;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;

class PaymentTable extends Component
{
    protected $layout = 'layouts.app';

    private const FINE_AMOUNT = 60.00;

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
    public float $finesPendientes   = 0;
    public int   $cantidadMultas    = 0;
    public $payments               = [];

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
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal', 'payments']);
        if (!$value) return;
        $this->cargarDeuda($value);
    }

    private function cargarDeuda(int $asociadoId): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal']);

        $asociado = Associate::find($asociadoId);
        if (!$asociado) return;

        $this->payments = Payment::where('associate_id', $asociadoId)
            ->latest()
            ->get();

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

        $this->cantidadMultas = EventAttendance::where('associate_id', $asociadoId)
            ->where('status', 'ausente')
            ->where('fine_paid', false)
            ->whereHas('event', fn($q) => $q->where('lista_cerrada', true))
            ->count();

        $this->finesPendientes = $this->cantidadMultas * self::FINE_AMOUNT;

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
        $this->totalFinal = $subtotal + ($this->aplicarMora ? $mora : 0) + $this->finesPendientes;
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
        if (!$this->asociado_id || (empty($this->mesesSeleccionados) && $this->finesPendientes <= 0)) {
            return null;
        }

        // Generar número correlativo
        $ultimo   = Payment::max('invoice_number');
        $nuevoNro = $ultimo ? intval($ultimo) + 1 : 1;
        $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

        $moraAplicada = $this->aplicarMora
            ? ($this->resumenDeuda['mora_calculada'] ?? 0)
            : 0;

        $fineAmount = $this->finesPendientes;
        $conceptParts = [];

        if (!empty($this->mesesSeleccionados)) {
            $conceptParts[] = 'PAGO DE MESES: ' . implode(', ', $this->mesesSeleccionados);
        }

        if ($fineAmount > 0) {
            $conceptParts[] = 'MULTAS POR FALTA: ' . $this->cantidadMultas . ' x S/ ' . number_format(self::FINE_AMOUNT, 2);
        }

        $concept = implode(' + ', $conceptParts) ?: 'PAGO DE CUOTA';

        $paymentType = empty($this->mesesSeleccionados) && $fineAmount > 0 ? 'falta' : 'cuota';

        // Guardar el pago
        $payment = Payment::create([
            'invoice_number'   => $invoiceNumber,
            'associate_id'     => $this->asociado_id,
            'amount'           => $this->totalFinal,
            'type'             => $paymentType,
            'concept'          => $concept,
            'months_paid'      => $this->mesesSeleccionados,
            'late_fee_applied' => $moraAplicada,
            'fine_amount'      => $fineAmount,
        ]);

        if ($fineAmount > 0) {
            EventAttendance::where('associate_id', $this->asociado_id)
                ->where('status', 'ausente')
                ->where('fine_paid', false)
                ->whereHas('event', fn($q) => $q->where('lista_cerrada', true))
                ->update(['fine_paid' => true]);
        }

        // Generar y descargar el recibo PDF
        return $this->generarReciboPDFActual($payment);
    }

    private function generarReciboPDFActual(Payment $payment): mixed
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
        $fineAmount   = (float) ($payment->fine_amount ?? 0);

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => $meses,
            'subtotal'      => $subtotal,
            'mora'          => $moraAplicada,
            'fine'          => $fineAmount,
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

    public function imprimirPago(int $paymentId): mixed
    {
        $payment = Payment::find($paymentId);
        if (!$payment || $payment->associate_id !== $this->asociado_id) {
            return null;
        }

        return $this->generarReciboPDF($payment);
    }

    private function generarReciboPDF(Payment $payment): mixed
    {
        $asociado = Associate::with('sector')->find($payment->associate_id);

        $receiptData = $this->buildReceiptData($payment);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        $data = array_merge($receiptData, [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'fecha_emision' => now()->format('d/m/Y H:i'),
        ]);

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            // Tamaño ticket 80mm
            $pdf->getDomPDF()->setPaper([0, 0, 226.77, 600], 'portrait');
            echo $pdf->output();
        }, 'recibo-' . $payment->invoice_number . '.pdf');
    }

    private function buildReceiptData(Payment $payment): array
    {
        $meses = collect($payment->months_paid ?: [])->map(function ($mes) use ($payment) {
            $baseAmount = max((float) $payment->amount - (float) ($payment->late_fee_applied ?? 0), 0);
            $periodos   = max(count($payment->months_paid ?: []), 1);
            $montoPorMes = round($baseAmount / $periodos, 2);

            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $montoPorMes,
            ];
        });

        $mora   = (float) ($payment->late_fee_applied ?? 0);
        $fine   = (float) ($payment->fine_amount ?? 0);

        $baseAmount = max((float) $payment->amount - $mora - $fine, 0);
        $periodos   = max(count($payment->months_paid ?: []), 1);
        $montoPorMes = round($baseAmount / $periodos, 2);

        $meses = collect($payment->months_paid ?: [])->map(function ($mes) use ($montoPorMes) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $montoPorMes,
            ];
        });

        $subtotal = round($meses->sum('monto'), 2);

        return [
            'meses'    => $meses,
            'subtotal' => $subtotal,
            'mora'     => $mora,
            'fine'     => $fine,
            'total'    => (float) $payment->amount,
        ];
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
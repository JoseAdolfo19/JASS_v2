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
    public string $search            = '';
    public $resumenDeuda             = null;
    public array $mesesSeleccionados = [];
    public float $montoCobrar        = 10;
    public bool  $aplicarMora        = true;
    public float $totalFinal         = 0;
    public int   $cantidadMultas     = 0;
    public float $finesPendientes    = 0;
    public $payments                 = [];

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
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

        // Solo pagos de tipo cuota (no multas)
        $this->payments = Payment::where('associate_id', $asociadoId)
            ->where('type', '!=', 'falta')
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

        $this->actualizarTotal();
    }

    // =========================================================================
    // CÁLCULO DE TOTALES
    // =========================================================================

    public function actualizarTotal(): void
    {
        if (!$this->resumenDeuda) return;

        $cantidad  = count($this->mesesSeleccionados);
        $subtotal  = $cantidad * $this->montoCobrar;

        $montoMora   = (float) Setting::get('mora_monto', 60);
        $mesesMora   = (int)   Setting::get('mora_meses', 3);
        $bloquesMora = $mesesMora > 0 ? floor($cantidad / $mesesMora) : 0;
        $mora        = $bloquesMora * $montoMora;

        $this->resumenDeuda['mora_calculada'] = $mora;
        $this->totalFinal = $subtotal + ($this->aplicarMora ? $mora : 0);
    }

    public function updatedAplicarMora(): void { $this->actualizarTotal(); }
    public function updatedMontoCobrar(): void { $this->actualizarTotal(); }

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
        if (!$this->asociado_id || empty($this->mesesSeleccionados)) {
            return null;
        }

        $ultimo        = Payment::max('invoice_number');
        $nuevoNro      = $ultimo ? intval($ultimo) + 1 : 1;
        $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

        $moraAplicada = $this->aplicarMora
            ? ($this->resumenDeuda['mora_calculada'] ?? 0)
            : 0;

        $concept = 'PAGO DE MESES: ' . implode(', ', $this->mesesSeleccionados);

        $payment = Payment::create([
            'invoice_number'   => $invoiceNumber,
            'associate_id'     => $this->asociado_id,
            'amount'           => $this->totalFinal,
            'type'             => 'cuota',
            'concept'          => $concept,
            'months_paid'      => $this->mesesSeleccionados,
            'late_fee_applied' => $moraAplicada,
            'fine_amount'      => 0,
        ]);

        return $this->generarReciboPDFActual($payment);
    }

    private function generarReciboPDFActual(Payment $payment): mixed
    {
        $asociado = Associate::with('sector')->find($this->asociado_id);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        $meses = collect($this->mesesSeleccionados)->map(function ($mes) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $this->montoCobrar,
            ];
        });

        $subtotal     = count($this->mesesSeleccionados) * $this->montoCobrar;
        $moraAplicada = $this->aplicarMora ? ($this->resumenDeuda['mora_calculada'] ?? 0) : 0;

        $meses_text = $meses->pluck('etiqueta')->implode(', ');
        $monto_en_letras = $this->numeroALetras($this->totalFinal);
        $fecha_recibo = now()->format('d \d\e F \d\e Y');

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => $meses,
            'subtotal'      => $subtotal,
            'mora'          => $moraAplicada,
            'fine'          => 0,
            'total'         => $this->totalFinal,
            'fecha_emision' => now()->format('d/m/Y H:i'),
            'multasDetalle' => [],
            'meses_text'    => $meses_text,
            'fecha_recibo'  => $fecha_recibo,
            'monto_en_letras' => $monto_en_letras,
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo', $data);
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

        $meses_text = $receiptData['meses']->pluck('etiqueta')->implode(', ');
        $monto_en_letras = $this->numeroALetras((float) $payment->amount);
        $fecha_recibo = now()->format('d \d\e F \d\e Y');

        $data = array_merge($receiptData, [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'fecha_emision' => now()->format('d/m/Y H:i'),
            'multasDetalle' => [],
            'meses_text'    => $meses_text,
            'fecha_recibo'  => $fecha_recibo,
            'monto_en_letras' => $monto_en_letras,
        ]);

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper([0, 0, 226.77, 600], 'portrait');
            echo $pdf->output();
        }, 'recibo-' . $payment->invoice_number . '.pdf');
    }

    private function buildReceiptData(Payment $payment): array
    {
        $mora        = (float) ($payment->late_fee_applied ?? 0);
        $fine        = (float) ($payment->fine_amount ?? 0);
        $baseAmount  = max((float) $payment->amount - $mora - $fine, 0);
        $periodos    = max(count($payment->months_paid ?: []), 1);
        $montoPorMes = round($baseAmount / $periodos, 2);

        $meses = collect($payment->months_paid ?: [])->map(function ($mes) use ($montoPorMes) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $montoPorMes,
            ];
        });

        return [
            'meses'            => $meses,
            'subtotal'         => round($meses->sum('monto'), 2),
            'mora'             => $mora,
            'fine'             => $fine,
            'total'            => (float) $payment->amount,
            'meses_text'       => $meses->pluck('etiqueta')->implode(', '),
            'fecha_recibo'     => now()->format('d \d\e F \d\e Y'),
            'monto_en_letras'  => $this->numeroALetras((float) $payment->amount),
        ];
    }

    private function numeroALetras(float $numero): string
    {
        $entero = floor($numero);
        $decimales = round(($numero - $entero) * 100);

        try {
            $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
            $texto = mb_strtoupper($formatter->format($entero));
        } catch (\Throwable $e) {
            $texto = strtoupper(number_format($entero, 0, ',', '.'));
        }

        return trim($texto) . ' CON ' . str_pad($decimales, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
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
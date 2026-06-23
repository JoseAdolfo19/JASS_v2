<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentTable extends Component
{
    protected $layout = 'layouts.app';

    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public $asociado_id;
    public ?int $connection_id       = null;  // Conexión/instalación seleccionada
    public $conexiones              = [];     // Todas las conexiones del asociado
    public string $search            = '';
    public $resumenDeuda             = null;
    public array $mesesSeleccionados = [];
    public bool  $aplicarMora        = true;
    public float $totalFinal         = 0;
    // `payments` se obtiene como propiedad computada para evitar serializar el paginador
    public int $payments_page        = 1; // página actual para historial de pagos
    public int $mesesAdelanto        = 5;  // cuántos meses futuros mostrar
    public bool  $usarMontoPersonalizado = false;   // toggle monto manual
    public ?float $montoPersonalizado    = null;    // monto que escribe el tesorero
    public ?string $errorMessage       = null;

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    // Tarifas históricas por año (sin posibilidad de edición manual)
    private const TARIFAS = [
        2013 => 3.00,
        2014 => 3.00,
        2015 => 3.00,
        2016 => 3.00,
        2017 => 3.00,
        2018 => 3.00,
        2019 => 3.00,
        2020 => 3.00,
        2021 => 3.00,
        2022 => 3.00,
        2023 => 4.00,
        2024 => 4.00,
        2025 => 10.00,
        2026 => 10.00,
    ];

    private function tarifaParaMes(string $mesAnio): float
    {
        $anio = (int) substr($mesAnio, 0, 4);
        return self::TARIFAS[$anio] ?? 10.00;
    }

    // =========================================================================
    // SELECCIÓN DE SOCIO
    // =========================================================================

    public function seleccionarSocio(int $id): void
    {
        // Limpiar completamente todo el estado anterior
        $this->reset([
            'search',
            'conexiones',
            'connection_id',
            'resumenDeuda',
            'mesesSeleccionados',
            'aplicarMora',
            'totalFinal',
            'usarMontoPersonalizado',
            'montoPersonalizado',
            'errorMessage'
        ]);
        
        // Ahora cargar el nuevo socio
        $this->asociado_id = $id;
        $this->payments_page = 1;
        $this->cargarConexiones($id);
    }

    public function updatedAsociadoId($value): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal', 'errorMessage', 'connection_id', 'conexiones']);
        if (!$value) return;
        $this->cargarConexiones($value);
    }

    public function updatedConnectionId($value): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal', 'errorMessage']);
        if (!$this->asociado_id || !$value) return;
        $this->payments_page = 1;
        $this->cargarDeuda($this->asociado_id, $value);
    }

    public function goToPaymentsPage(int $page): void
    {
        $this->payments_page = max(1, $page);
        if ($this->asociado_id) {
            $this->cargarDeuda($this->asociado_id, $this->connection_id);
        }
    }

    // Propiedad computada que devuelve el paginador de `payments` para la vista.
    public function getPaymentsProperty()
    {
        if (!$this->asociado_id) {
            return Payment::whereRaw('0 = 1')->paginate(5, ['*'], 'payments_page', $this->payments_page);
        }

        $connectionId = $this->connection_id;
        if (!$connectionId) {
            $connectionId = Associate::find($this->asociado_id)?->connections()->where('is_primary', true)->value('id');
        }

        if (!$connectionId) {
            return Payment::whereRaw('0 = 1')->paginate(5, ['*'], 'payments_page', $this->payments_page);
        }

        return Payment::where('associate_id', $this->asociado_id)
            ->where('connection_id', $connectionId)
            ->where('type', '!=', 'falta')
            ->latest()
            ->paginate(5, ['*'], 'payments_page', $this->payments_page);
    }

    private function cargarConexiones(int $asociadoId): void
    {
        $asociado = Associate::with('connections')->find($asociadoId);
        if (!$asociado) {
            $this->conexiones = [];
            return;
        }

        $this->conexiones = $asociado->connections()
            ->orderBy('is_primary', 'desc')
            ->orderBy('label', 'asc')
            ->get()
            ->map(fn($conn) => [
                'id'    => $conn->id,
                'label' => $conn->is_primary ? 'Casa Titular' : $conn->label,
            ])
            ->toArray();

        // Auto-seleccionar la conexión principal
        if (!empty($this->conexiones)) {
            // Buscar la conexión primaria en el array
            $primary = collect($this->conexiones)
                ->where('label', 'Casa Titular')
                ->first();
            
            $this->connection_id = $primary['id'] ?? $this->conexiones[0]['id'];
            $this->cargarDeuda($asociadoId, $this->connection_id);
        }
    }

    private function obtenerMesesPagados(int $asociadoId, ?int $connectionId = null, bool $lock = false): array
    {
        $query = Payment::where('associate_id', $asociadoId)
            ->where('type', 'cuota');

        if ($connectionId) {
            $query->where('connection_id', $connectionId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()
            ->pluck('months_paid')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function obtenerMesesDuplicados(array $meses, bool $lock = false): array
    {
        if (!$this->asociado_id) {
            return [];
        }

        $mesesPagados = $this->obtenerMesesPagados($this->asociado_id, $this->connection_id, $lock);

        return array_values(array_intersect($meses, $mesesPagados));
    }

    private function cargarDeuda(int $asociadoId, ?int $connectionId = null): void
    {
        $this->reset(['mesesSeleccionados', 'resumenDeuda', 'totalFinal',
                   'usarMontoPersonalizado', 'montoPersonalizado', 'errorMessage']);

        $asociado = Associate::find($asociadoId);
        if (!$asociado) return;

        // Obtener la conexión para acceder a su fecha de inicio
        $connection = null;
        if ($connectionId) {
            $connection = $asociado->connections()->where('id', $connectionId)->first();
        } elseif (!empty($this->conexiones)) {
            // Fallback: obtener la conexión principal
            $connection = $asociado->connections()->where('is_primary', true)->first();
            if ($connection) {
                $connectionId = $connection->id;
            }
        }

        if (!$connection) {
            return;
        }

        // El paginador de `payments` se obtiene como propiedad computada (getPaymentsProperty)

        $fechaInicio = $connection->entry_date
            ? Carbon::parse($connection->entry_date)->startOfMonth()
            : Carbon::parse($connection->created_at)->startOfMonth();

        $mesActual  = Carbon::now()->startOfMonth();
        $mesLimite  = $mesActual->copy()->addMonths(max(1, $this->mesesAdelanto));

        // Meses ya pagados (solo para esta conexión)
        $mesesPagados = $this->obtenerMesesPagados($asociadoId, $connection->id);

        // Construir lista: deudas pasadas + mes actual + meses futuros (adelantos)
        $listaDeuda    = [];
        $deudaAgrupada = [];
        $tempFecha     = $fechaInicio->copy();

        while ($tempFecha->lessThan($mesLimite)) {
            $mesAnio  = $tempFecha->format('Y-m');
            $esFuturo = $tempFecha->greaterThan($mesActual);

            if (!in_array($mesAnio, $mesesPagados)) {
                $listaDeuda[]    = $mesAnio;
                $deudaAgrupada[] = [
                    'etiqueta'  => strtoupper(Carbon::parse($mesAnio)->translatedFormat('F Y')),
                    'meses'     => [$mesAnio],
                    'adelanto'  => $esFuturo,
                    'tarifa'    => $this->tarifaParaMes($mesAnio),
                ];
            }

            $tempFecha->addMonth();
        }

        $this->resumenDeuda       = ['items' => $deudaAgrupada, 'mora_calculada' => 0];
        $this->mesesSeleccionados = $listaDeuda;

        $this->actualizarTotal();
    }

    public function updatedMesesAdelanto(): void
    {
        if ($this->asociado_id) {
            $this->cargarDeuda($this->asociado_id, $this->connection_id);
        }
    }

    // =========================================================================
    // CÁLCULO DE TOTALES
    // =========================================================================

    public function actualizarTotal(): void
    {
        if (!$this->resumenDeuda) return;

        $mesActual = Carbon::now()->startOfMonth()->format('Y-m');

        // Subtotal calculado con tarifa histórica por cada mes seleccionado
        $subtotal = collect($this->mesesSeleccionados)
            ->sum(fn($m) => $this->tarifaParaMes($m));

        // Mora solo sobre meses vencidos
        $mesesVencidos = collect($this->mesesSeleccionados)
            ->filter(fn($m) => $m <= $mesActual)
            ->count();

        $montoMora   = (float) Setting::get('mora_monto', 60);
        $mesesMora   = (int)   Setting::get('mora_meses', 3);
        $bloquesMora = $mesesMora > 0 ? floor($mesesVencidos / $mesesMora) : 0;
        $mora        = $bloquesMora * $montoMora;

        // Subtotales por grupo de tarifa (para mostrar en vista y boleta)
        $grupos = collect($this->mesesSeleccionados)
            ->groupBy(fn($m) => $this->tarifaParaMes($m))
            ->map(fn($meses, $tarifa) => [
                'tarifa'   => (float) $tarifa,
                'cantidad' => $meses->count(),
                'subtotal' => round($meses->count() * $tarifa, 2),
            ])
            ->sortBy('tarifa')
            ->values()
            ->toArray();

        $this->resumenDeuda['mora_calculada'] = $mora;
        $this->resumenDeuda['grupos_tarifa']  = $grupos;
        $this->resumenDeuda['subtotal']       = round($subtotal, 2);

        // Si el tesorero ingresó un monto manual, usarlo como total final
        // pero conservar el desglose histórico solo para referencia
        if ($this->usarMontoPersonalizado && $this->montoPersonalizado !== null && $this->montoPersonalizado > 0) {
            $this->totalFinal = round((float) $this->montoPersonalizado, 2);
        } else {
            $this->totalFinal = round($subtotal + ($this->aplicarMora ? $mora : 0), 2);
        }
    }

    public function updatedAplicarMora(): void          { $this->actualizarTotal(); }
    public function updatedUsarMontoPersonalizado(): void { $this->actualizarTotal(); }
    public function updatedMontoPersonalizado(): void     { $this->actualizarTotal(); }

    public function toggleMes(string $mes): void
    {
        $this->errorMessage = null;

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

        return DB::transaction(function () {
            $mesesDuplicados = $this->obtenerMesesDuplicados($this->mesesSeleccionados);

            if (!empty($mesesDuplicados)) {
                $this->errorMessage = 'Se ha evitado un cobro duplicado. Ya se cobraron los meses: ' . implode(', ', $mesesDuplicados) . '.';
                $this->mesesSeleccionados = array_values(array_diff($this->mesesSeleccionados, $mesesDuplicados));
                $this->actualizarTotal();
                return null;
            }

            $ultimo        = Payment::where('type', 'cuota')->max('invoice_number');
            $nuevoNro      = $ultimo ? intval($ultimo) + 1 : 1;
            $invoiceNumber = str_pad($nuevoNro, 6, '0', STR_PAD_LEFT);

            $moraAplicada = $this->aplicarMora
                ? ($this->resumenDeuda['mora_calculada'] ?? 0)
                : 0;

            $mesActual     = Carbon::now()->startOfMonth()->format('Y-m');
            $mesesDeuda    = array_filter($this->mesesSeleccionados, fn($m) => $m <= $mesActual);
            $mesesAdelanto = array_filter($this->mesesSeleccionados, fn($m) => $m > $mesActual);

            $conceptParts = [];
            if (!empty($mesesDeuda))    $conceptParts[] = 'CUOTAS: ' . implode(', ', $mesesDeuda);
            if (!empty($mesesAdelanto)) $conceptParts[] = 'ADELANTO: ' . implode(', ', $mesesAdelanto);
            $concept = implode(' + ', $conceptParts) ?: 'PAGO DE CUOTA';

            if ($this->usarMontoPersonalizado && $this->montoPersonalizado > 0) {
                $concept .= ' [MONTO AJUSTADO]';
            }

            $payment = Payment::create([
                'invoice_number'   => $invoiceNumber,
                'associate_id'     => $this->asociado_id,
                'connection_id'    => $this->connection_id,
                'amount'           => $this->totalFinal,
                'type'             => 'cuota',
                'concept'          => $concept,
                'months_paid'      => $this->mesesSeleccionados,
                'late_fee_applied' => $moraAplicada,
                'fine_amount'      => 0,
            ]);

            return $this->generarReciboPDFActual($payment);
        });
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

        $mesActualStr = Carbon::now()->startOfMonth()->format('Y-m');

        $meses = collect($this->mesesSeleccionados)->map(function ($mes) use ($mesActualStr) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $this->tarifaParaMes($mes),
                'adelanto' => $mes > $mesActualStr,
            ];
        });

        $subtotal     = $this->resumenDeuda['subtotal'] ?? $meses->sum('monto');
        $gruposTarifa = $this->resumenDeuda['grupos_tarifa'] ?? [];
        $moraAplicada = $this->aplicarMora ? ($this->resumenDeuda['mora_calculada'] ?? 0) : 0;

        $data = [
            'jass'          => $jass,
            'asociado'      => $asociado,
            'payment'       => $payment,
            'meses'         => $meses,
            'grupos_tarifa' => $gruposTarifa,
            'subtotal'      => $subtotal,
            'mora'          => $moraAplicada,
            'fine'          => 0,
            'total'         => $this->totalFinal,
            'fecha_emision' => now()->format('d/m/Y H:i'),
            'multasDetalle' => [],
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo-4x-hoja', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->setPaper('a4', 'portrait');
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
            'multasDetalle' => [],
        ]);

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.recibo-4x-hoja', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, 'recibo-' . $payment->invoice_number . '.pdf');
    }

    private function buildReceiptData(Payment $payment): array
    {
        $mora = (float) ($payment->late_fee_applied ?? 0);
        $fine = (float) ($payment->fine_amount ?? 0);

        $mesActualStr = Carbon::now()->startOfMonth()->format('Y-m');

        // Reconstruir con tarifa histórica por mes
        $meses = collect($payment->months_paid ?: [])->map(function ($mes) use ($mesActualStr) {
            return [
                'etiqueta' => strtoupper(Carbon::parse($mes)->translatedFormat('F Y')),
                'monto'    => $this->tarifaParaMes($mes),
                'adelanto' => $mes > $mesActualStr,
            ];
        });

        // Subtotales agrupados por tarifa
        $gruposTarifa = $meses->groupBy('monto')
            ->map(fn($items, $tarifa) => [
                'tarifa'   => (float) $tarifa,
                'cantidad' => $items->count(),
                'subtotal' => round($items->count() * $tarifa, 2),
            ])
            ->sortBy('tarifa')
            ->values()
            ->toArray();

        $subtotal = round($meses->sum('monto'), 2);

        return [
            'meses'         => $meses,
            'grupos_tarifa' => $gruposTarifa,
            'subtotal'      => $subtotal,
            'mora'          => $mora,
            'fine'          => $fine,
            'total'         => (float) $payment->amount,
        ];
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function incrementarAdelanto(): void
    {
        $this->mesesAdelanto++;
        if ($this->asociado_id) $this->cargarDeuda($this->asociado_id, $this->connection_id);
    }

    public function decrementarAdelanto(): void
    {
        $this->mesesAdelanto = max(1, $this->mesesAdelanto - 1);
        if ($this->asociado_id) $this->cargarDeuda($this->asociado_id);
    }

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
            'associates'   => $associates,
            'mesesAdelanto' => $this->mesesAdelanto,
            'payments'      => $this->payments,
        ]);
    }
}
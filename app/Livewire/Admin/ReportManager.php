<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Sector;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport; // Para exportar asistencias a Excel

class ReportManager extends Component
{
    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public string $tab          = 'diario';
    public string $fecha_filtro = '';

    // Formulario de cobro
    public $associate_id;
    public string $type    = 'cuota';
    public $amount;
    public $concept;
    public $detail;
    public $multa_tipo;
    public $otros_concepto;

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->fecha_filtro = date('Y-m-d');
    }

    // =========================================================================
    // PROPIEDADES COMPUTADAS (deuda del socio seleccionado)
    // =========================================================================

    public function getMesesDeudaProperty(): int
    {
        if (!$this->associate_id) return 0;

        $associate = Associate::find($this->associate_id);
        if (!$associate) return 0;

        $fechaInicio = $associate->entry_date
            ? Carbon::parse($associate->entry_date)->startOfMonth()
            : Carbon::parse($associate->created_at)->startOfMonth();

        return $fechaInicio->diffInMonths(Carbon::now()->startOfMonth());
    }

    public function getTextoDeudaProperty(): string
    {
        $meses = $this->meses_deuda;
        if ($meses <= 0) return 'Sin deuda pendiente';

        $plural = $meses > 1 ? 'es' : '';
        return "Deuda de {$meses} mes{$plural} pendiente{$plural}";
    }

    public function getMoraTotalProperty(): float
    {
        $meses = $this->meses_deuda;
        if ($meses < 3) return 0;

        return floor($meses / 3) * 60;
    }

    // =========================================================================
    // ACCIONES DEL FORMULARIO
    // =========================================================================

    public function savePayment(): void
    {
        $this->validate([
            'associate_id' => 'required|exists:associates,id',
            'type'         => 'required|in:cuota,falta,extra',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $ultimo   = Payment::max('invoice_number');
        $nuevoNro = $ultimo ? intval($ultimo) + 1 : 1;

        Payment::create([
            'invoice_number' => str_pad($nuevoNro, 6, '0', STR_PAD_LEFT),
            'associate_id'   => $this->associate_id,
            'amount'         => $this->amount,
            'type'           => $this->type,
            'concept'        => $this->concept ?: 'Pago registrado',
            'detail'         => $this->detail,
        ]);

        $this->reset(['associate_id', 'type', 'amount', 'concept', 'detail', 'multa_tipo', 'otros_concepto']);

        session()->flash('message', 'Pago registrado correctamente.');
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Corrige strings con codificación incorrecta (Latin-1 guardado como UTF-8).
     * Se aplica sobre strings que vienen de la BD en get*Data().
     */
    private function fixUtf8(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
    }

    /**
     * Sanitiza datos para exportación PDF (escapa HTML, múltiples encodings).
     * Se aplica en buildPdf() antes de renderizar la vista PDF.
     */
    private function sanitizeForPDF(mixed $data): mixed
    {
        if (is_string($data)) {
            foreach (['ISO-8859-1', 'Windows-1252', 'CP1252'] as $encoding) {
                $converted = @iconv($encoding, 'UTF-8//TRANSLIT//IGNORE', $data);
                if ($converted !== false && $converted !== $data) {
                    $data = $converted;
                    break;
                }
            }

            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data) ?? $data;

            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }

            $data = html_entity_decode($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        }

        if (is_array($data)) {
            return array_map([$this, 'sanitizeForPDF'], $data);
        }

        if ($data instanceof Collection) {
            return $data->map(fn($item) => $this->sanitizeForPDF($item));
        }

        if (is_object($data)) {
            foreach (get_object_vars($data) as $key => $value) {
                $data->$key = $this->sanitizeForPDF($value);
            }
            return $data;
        }

        return $data;
    }

    /**
     * Construye el array estandarizado de un asociado para los reportes.
     */
    private function buildAssociateArray(Associate $associate): array
    {
        return [
            'name'      => $this->fixUtf8($associate->name ?? ''),
            'last_name' => $this->fixUtf8($associate->last_name ?? ''),
            'sector'    => $this->fixUtf8($associate->sector?->name ?? 'Sin sector'),
        ];
    }

    /**
     * Calcula la mora según bloques de 3 meses (S/60 por bloque).
     */
    private function calcularMora(int $mesesDeuda): float
    {
        if ($mesesDeuda < 3) return 0;
        return floor($mesesDeuda / 3) * 60;
    }

    /**
     * Calcula los meses de deuda de un asociado desde su fecha de ingreso.
     */
    private function calcularMesesDeuda(Associate $associate): int
    {
        $fechaInicio = $associate->entry_date
            ? Carbon::parse($associate->entry_date)->startOfMonth()
            : Carbon::parse($associate->created_at)->startOfMonth();

        return $fechaInicio->diffInMonths(Carbon::now()->startOfMonth());
    }

    /**
     * Genera y descarga un PDF usando streamDownload (compatible con Livewire).
     * pdf->download() no funciona en Livewire porque choca con la respuesta JSON.
     */
    private function buildPdf(string $view, mixed $data, string $filename): mixed
    {
        $data = $this->sanitizeForPDF($data);

        return response()->streamDownload(function () use ($view, $data) {
            $pdf = Pdf::loadView($view, compact('data'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, $filename . '-' . date('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // DATOS PARA REPORTES
    // =========================================================================

    /**
     * 1. Padrón General de Morosos
     * Socios sin pagos en los últimos 2 meses.
     */
    public function getMorososData(): Collection
    {
        $montoCuota = 10;

        return Associate::with(['sector', 'payments' => fn($q) => $q->latest()])
            ->whereDoesntHave('payments', fn($q) => $q->where('created_at', '>=', Carbon::now()->subMonths(2)))
            ->get()
            ->map(function (Associate $associate) use ($montoCuota) {
                $mesesDeuda = $this->calcularMesesDeuda($associate);
                $subtotal   = $mesesDeuda * $montoCuota;
                $mora       = $this->calcularMora($mesesDeuda);

                return [
                    'associate'   => $this->buildAssociateArray($associate),
                    'meses_deuda' => $mesesDeuda,
                    'subtotal'    => $subtotal,
                    'mora'        => $mora,
                    'total'       => $subtotal + $mora,
                ];
            });
    }

    /**
     * 2. Balance de Caja
     * Totales de ingresos, egresos y saldo disponible.
     */
    public function getBalanceData(): array
    {
        $ingresos = Payment::sum('amount');
        $egresos  = Expense::sum('amount');

        return [
            'ingresos' => (float) $ingresos,
            'egresos'  => (float) $egresos,
            'saldo'    => (float) ($ingresos - $egresos),
        ];
    }

    /**
     * 3. Reporte de Altas y Bajas
     * Variaciones en el padrón de socios del año actual.
     */
    public function getAltasBajasData(): array
    {
        $anio = (int) date('Y');

        $altas = Associate::whereYear('created_at', $anio)->count();
        $bajas = Associate::onlyTrashed()->whereYear('deleted_at', $anio)->count();

        return [
            'altas'            => $altas,
            'bajas'            => $bajas,
            'crecimiento_neto' => $altas - $bajas,
        ];
    }

    /**
     * 4. Resumen de Deuda por Multas
     * Socios con pagos de tipo 'falta' agrupados.
     */
    public function getMultasData(): Collection
    {
        return Payment::where('type', 'falta')
            ->with('associate.sector')
            ->get()
            ->groupBy('associate_id')
            ->map(function (Collection $pagos, int $associateId) {
                $associate = Associate::find($associateId);

                if (!$associate) return null;

                return [
                    'associate'       => $this->buildAssociateArray($associate),
                    'cantidad_multas' => $pagos->count(),
                    'total_multas'    => (float) $pagos->sum('amount'),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * 5. Lista de Aptos para Corte
     * Socios sin pagos en los últimos 6 meses.
     */
    public function getAptosParaCorteData(): Collection
    {
        return Associate::with('sector')
            ->whereDoesntHave('payments', fn($q) => $q->where('created_at', '>=', Carbon::now()->subMonths(6)))
            ->get()
            ->map(fn(Associate $associate) => [
                'associate'   => $this->buildAssociateArray($associate),
                'meses_deuda' => $this->calcularMesesDeuda($associate),
            ]);
    }

    // =========================================================================
    // EXPORTACIONES PDF
    // =========================================================================

    public function exportMorososPDF(): mixed
    {
        return $this->buildPdf('pdf.reportes.morosos', $this->getMorososData(), 'padron-morosos');
    }

    public function exportBalancePDF(): mixed
    {
        return $this->buildPdf('pdf.reportes.balance', $this->getBalanceData(), 'balance-caja');
    }

    public function exportAltasBajasPDF(): mixed
    {
        return $this->buildPdf('pdf.reportes.altas-bajas', $this->getAltasBajasData(), 'altas-bajas');
    }

    public function exportMultasPDF(): mixed
    {
        return $this->buildPdf('pdf.reportes.multas', $this->getMultasData(), 'deuda-multas');
    }

    public function exportAptosCortePDF(): mixed
    {
        return $this->buildPdf('pdf.reportes.aptos-corte', $this->getAptosParaCorteData(), 'aptos-corte');
    }
    public function exportAttendanceExcel($eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            session()->flash('error', 'El evento no existe.');
            return;
        }

        $filename = 'Asistencia_' . str_replace(' ', '_', $event->name) . '.xlsx';

        // Esto dispara la descarga del Excel con múltiples hojas
        return Excel::download(new AttendanceExport($eventId), $filename);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        // Resumen de caja del día filtrado
        $resumenDiario = Payment::whereDate('created_at', $this->fecha_filtro)
            ->select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('type')
            ->get();

        // Lista de morosos sin mapear (para tabla principal)
        $morosos = Associate::with(['sector', 'payments' => fn($q) => $q->latest()])
            ->whereDoesntHave('payments', fn($q) => $q->where('created_at', '>=', Carbon::now()->subMonths(2)))
            ->get();

        // Padrón completo ordenado
        $associates = Associate::orderBy('last_name')->get();

        // Historial de pagos paginado
        $payments = Payment::with('associate')->latest()->paginate(10);

        // Sectores con sus socios
        $sectoresConSocios = Sector::with(['associates' => fn($q) => $q->orderBy('last_name')])
            ->withCount('associates')
            ->get();

        return view('livewire.admin.report-manager', [
            // Datos generales
            'resumenDiario'     => $resumenDiario,
            'totalGeneral'      => $resumenDiario->sum('total'),
            'morosos'           => $morosos,
            'associates'        => $associates,
            'payments'          => $payments,
            'sectoresConSocios' => $sectoresConSocios,
            // Datos para las tarjetas de reporte
            'morososData'       => $this->getMorososData(),
            'balanceData'       => $this->getBalanceData(),
            'altasBajasData'    => $this->getAltasBajasData(),
            'multasData'        => $this->getMultasData(),
            'aptosCorteData'    => $this->getAptosParaCorteData(),
        ])->layout('layouts.app');
    }
}

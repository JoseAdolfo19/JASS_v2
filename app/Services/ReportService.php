<?php

namespace App\Services;

use App\Models\Associate;
use App\Models\Payment;
use App\Models\Sector;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDailySummary(string $fecha): Collection
    {
        return Payment::whereDate('created_at', $fecha)
            ->select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('type')
            ->get();
    }

    public function getMorososData(): Collection
    {
        $montoCuota = (float) Setting::get('cuota_mensual', 10);

        return Associate::with(['sector', 'payments' => fn($query) => $query->latest()])
            ->whereDoesntHave('payments', fn($query) => $query->where('created_at', '>=', Carbon::now()->subMonths(2)))
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

    public function getMorososModels(): Collection
    {
        return Associate::with(['sector', 'payments' => fn($query) => $query->latest()])
            ->whereDoesntHave('payments', fn($query) => $query->where('created_at', '>=', Carbon::now()->subMonths(2)))
            ->get();
    }

    public function getAllAssociates(): Collection
    {
        return Associate::orderBy('last_name')->get();
    }

    public function getPaymentsPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Payment::with('associate')->latest()->paginate($perPage);
    }

    public function getSectoresConSocios(): Collection
    {
        return Sector::with(['associates' => fn($query) => $query->orderBy('last_name')])
            ->withCount('associates')
            ->get();
    }

    public function getBalanceData(): array
    {
        $ingresos = Payment::sum('amount');
        $egresos  = \App\Models\Expense::sum('amount');

        return [
            'ingresos' => (float) $ingresos,
            'egresos'  => (float) $egresos,
            'saldo'    => (float) ($ingresos - $egresos),
        ];
    }

    public function getAltasBajasData(): array
    {
        $anio = (int) date('Y');

        $altas = Associate::whereYear('created_at', $anio)->count();
        $bajas = Associate::onlyTrashed()->whereYear('deleted_at', $anio)->count();
        $suspendidos = Associate::where('status', 'suspendido')->count();

        return [
            'altas'            => $altas,
            'bajas'            => $bajas,
            'suspendidos'      => $suspendidos,
            'crecimiento_neto' => $altas - $bajas,
        ];
    }

    public function getMultasData(): Collection
    {
        return Payment::where('type', 'falta')
            ->with('associate.sector')
            ->get()
            ->groupBy('associate_id')
            ->map(function (Collection $pagos, int $associateId) {
                $associate = $pagos->first()->associate;
                if (!$associate) {
                    return null;
                }

                return [
                    'associate'       => $this->buildAssociateArray($associate),
                    'cantidad_multas' => $pagos->count(),
                    'total_multas'    => (float) $pagos->sum('amount'),
                ];
            })
            ->filter()
            ->values();
    }

    public function getAptosParaCorteData(): Collection
    {
        return Associate::with('sector')
            ->whereDoesntHave('payments', fn($query) => $query->where('created_at', '>=', Carbon::now()->subMonths(6)))
            ->get()
            ->map(fn(Associate $associate) => [
                'associate'   => $this->buildAssociateArray($associate),
                'meses_deuda' => $this->calcularMesesDeuda($associate),
            ]);
    }

    private function buildAssociateArray(Associate $associate): array
    {
        return [
            'name'      => $this->fixUtf8($associate->name ?? ''),
            'last_name' => $this->fixUtf8($associate->last_name ?? ''),
            'sector'    => $this->fixUtf8($associate->sector?->name ?? 'Sin sector'),
        ];
    }

    private function calcularMora(int $mesesDeuda): float
    {
        if ($mesesDeuda < 3) {
            return 0;
        }

        return floor($mesesDeuda / 3) * 60;
    }

    private function calcularMesesDeuda(Associate $associate): int
    {
        $fechaInicio = $associate->entry_date
            ? Carbon::parse($associate->entry_date)->startOfMonth()
            : Carbon::parse($associate->created_at)->startOfMonth();

        return $fechaInicio->diffInMonths(Carbon::now()->startOfMonth());
    }

    private function fixUtf8(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
    }
}

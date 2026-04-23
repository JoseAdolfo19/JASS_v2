<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\Associate;
use App\Models\Expense;
use Livewire\Component;
use Carbon\Carbon;

class Home extends Component
{
    public function render()
    {
        // 1. Calcular Recaudación Total (Ingresos)
        $ingresos = Payment::sum('amount');

        // 2. Calcular Egresos
        $egresos = Expense::sum('amount');

        // 3. Calcular Saldo
        $saldo = $ingresos - $egresos;

        // 4. Número de Socios
        $numeroSocios = Associate::count();

        // 5. Calcular Socios Morosos (Deudores)
        $mesActual = Carbon::now()->format('Y-m');
        $numeroDeudores = Associate::whereDoesntHave('payments', function($query) use ($mesActual) {
            $query->where('months_paid', 'like', "%$mesActual%");
        })->count();

        // 6. Obtener últimos 5 pagos para la tabla
        $ultimosPagos = Payment::with('associate')->latest()->take(5)->get();

        return view('livewire.admin.home', [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo' => $saldo,
            'numeroSocios' => $numeroSocios,
            'numeroDeudores' => $numeroDeudores,
            'ultimosPagos' => $ultimosPagos,
        ])->layout('layouts.app');
    }
}
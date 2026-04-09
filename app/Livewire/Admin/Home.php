<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\Associate;
use Livewire\Component;
use Carbon\Carbon;

class Home extends Component
{
    public function render()
    {
        // 1. Calcular Recaudación Total
        $totalRecaudado = Payment::sum('amount');

        // 2. Calcular Socios Morosos (Lógica simple: que no tengan pagos este mes)
        $mesActual = Carbon::now()->format('Y-m');
        $cantidadMorosos = Associate::whereDoesntHave('payments', function($query) use ($mesActual) {
            $query->where('months_paid', 'like', "%$mesActual%");
        })->count();

        // 3. Obtener últimos 5 pagos para la tabla
        $ultimosPagos = Payment::with('associate')->latest()->take(5)->get();

        return view('livewire.admin.home', [
            'totalRecaudado' => $totalRecaudado,
            'cantidadMorosos' => $cantidadMorosos,
            'ultimosPagos' => $ultimosPagos,
        ])->layout('layouts.app');
    }
}
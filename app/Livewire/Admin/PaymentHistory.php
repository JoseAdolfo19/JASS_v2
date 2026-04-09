<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;
    protected $layout = 'layouts.app';
    public $search = '';

    public function render()
    {
        $payments = Payment::with('associate')
            ->whereHas('associate', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('invoice_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payment-history', ['payments' => $payments]);
    }
}
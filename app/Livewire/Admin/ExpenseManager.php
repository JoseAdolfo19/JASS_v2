<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // =========================================================================
    // PROPIEDADES DEL FORMULARIO
    // =========================================================================

    public string $description    = '';
    public string $category       = 'Otros';
    public $amount;
    public string $date           = '';
    public string $voucher_number = '';
    public $voucher;              // Archivo temporal de Livewire

    // =========================================================================
    // PROPIEDADES DE UI
    // =========================================================================

    public string $search         = '';
    public ?int $confirmingDelete = null; // ID del gasto a eliminar

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    protected function rules(): array
    {
        return [
            'description'    => 'required|min:3',
            'category'       => 'required|in:Materiales,Servicios,Planilla,Viáticos,Otros',
            'amount'         => 'required|numeric|min:0.01',
            'date'           => 'required|date',
            'voucher_number' => 'nullable|string|max:100',
            'voucher'        => 'nullable|image|max:4096', // max 4MB
        ];
    }

    protected $messages = [
        'description.required' => 'La descripción es obligatoria.',
        'description.min'      => 'La descripción debe tener al menos 3 caracteres.',
        'amount.required'      => 'El monto es obligatorio.',
        'amount.numeric'       => 'El monto debe ser un número.',
        'amount.min'           => 'El monto debe ser mayor a 0.',
        'date.required'        => 'La fecha es obligatoria.',
        'voucher.image'        => 'El comprobante debe ser una imagen (JPG, PNG, etc).',
        'voucher.max'          => 'La imagen no debe superar 4MB.',
    ];

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->date = date('Y-m-d');
    }

    // =========================================================================
    // ACCIONES
    // =========================================================================

    public function save(): void
    {
        $this->validate();

        $voucherPath = null;

        // Si se subió una imagen, guardarla en storage/app/public/vouchers
        if ($this->voucher) {
            $voucherPath = $this->voucher->store('vouchers', 'public');
        }

        Expense::create([
            'description'    => $this->description,
            'category'       => $this->category,
            'amount'         => $this->amount,
            'date'           => $this->date,
            'voucher_number' => $this->voucher_number ?: null,
            'voucher_path'   => $voucherPath,
        ]);

        $this->resetForm();
        session()->flash('message', 'Gasto registrado correctamente.');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
    }

    public function delete(int $id): void
    {
        $expense = Expense::find($id);

        if (!$expense) return;

        // Eliminar imagen del storage si existe
        if ($expense->voucher_path && \Storage::disk('public')->exists($expense->voucher_path)) {
            \Storage::disk('public')->delete($expense->voucher_path);
        }

        $expense->delete();

        $this->confirmingDelete = null;
        session()->flash('message', 'Gasto eliminado correctamente.');
    }

    private function resetForm(): void
    {
        $this->reset(['description', 'amount', 'voucher_number', 'voucher']);
        $this->category = 'Otros';
        $this->date     = date('Y-m-d');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $expenses = Expense::where('description', 'like', '%' . $this->search . '%')
            ->latest('date')
            ->paginate(10);

        $totalMes = Expense::whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('amount');

        $totalGeneral = Expense::sum('amount');

        return view('livewire.admin.expense-manager', [
            'expenses'     => $expenses,
            'totalMes'     => $totalMes,
            'totalGeneral' => $totalGeneral,
        ])->layout('layouts.app');
    }
}
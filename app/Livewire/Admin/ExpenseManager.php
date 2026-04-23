<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
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
    public string $voucher_type   = 'boleta';   // tipo de comprobante
    public string $beneficiary    = '';          // proveedor / beneficiario
    public string $ruc_dni        = '';          // RUC o DNI
    public string $notes          = '';          // observaciones
    public $voucher;                             // imagen del comprobante

    // =========================================================================
    // PROPIEDADES DE UI
    // =========================================================================

    public string $search         = '';
    public string $filterType     = '';          // filtro por tipo de comprobante
    public ?int $confirmingDelete = null;

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
            'voucher_type'   => 'required|in:boleta,factura,recibo_honorarios,declaracion_jurada,otro',
            'voucher_number' => 'nullable|string|max:100',
            'beneficiary'    => 'nullable|string|max:200',
            'ruc_dni'        => 'nullable|string|max:20',
            'notes'          => 'nullable|string|max:500',
            'voucher'        => 'nullable|image|max:4096',
        ];
    }

    protected $messages = [
        'description.required' => 'La descripción es obligatoria.',
        'description.min'      => 'Mínimo 3 caracteres.',
        'amount.required'      => 'El monto es obligatorio.',
        'amount.numeric'       => 'El monto debe ser un número.',
        'amount.min'           => 'El monto debe ser mayor a 0.',
        'date.required'        => 'La fecha es obligatoria.',
        'voucher.image'        => 'Debe ser una imagen (JPG, PNG).',
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
    // WATCHERS
    // =========================================================================

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedFilterType(): void { $this->resetPage(); }

    // =========================================================================
    // GUARDAR EGRESO
    // =========================================================================

    public function save(): void
    {
        $this->validate();

        $voucherPath = null;
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
            'voucher_type'   => $this->voucher_type,
            'beneficiary'    => $this->beneficiary ?: null,
            'ruc_dni'        => $this->ruc_dni ?: null,
            'notes'          => $this->notes ?: null,
        ]);

        $this->resetForm();
        session()->flash('message', 'Egreso registrado correctamente.');
    }

    // =========================================================================
    // GENERAR PDF DEL COMPROBANTE
    // =========================================================================

    public function generarPDF(int $id): mixed
    {
        $expense = Expense::findOrFail($id);

        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        return response()->streamDownload(function () use ($expense, $jass) {
            $pdf = Pdf::loadView('pdf.comprobante-egreso', compact('expense', 'jass'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, 'egreso-' . $expense->id . '-' . $expense->date->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // ELIMINAR
    // =========================================================================

    public function confirmDelete(int $id): void { $this->confirmingDelete = $id; }
    public function cancelDelete(): void          { $this->confirmingDelete = null; }

    public function delete(int $id): void
    {
        $expense = Expense::find($id);
        if (!$expense) return;

        if ($expense->voucher_path && \Storage::disk('public')->exists($expense->voucher_path)) {
            \Storage::disk('public')->delete($expense->voucher_path);
        }

        $expense->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Egreso eliminado correctamente.');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function resetForm(): void
    {
        $this->reset(['description', 'amount', 'voucher_number', 'voucher', 'beneficiary', 'ruc_dni', 'notes']);
        $this->category     = 'Otros';
        $this->voucher_type = 'boleta';
        $this->date         = date('Y-m-d');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $expenses = Expense::when($this->search, fn($q) =>
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('beneficiary', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterType, fn($q) => $q->where('voucher_type', $this->filterType))
            ->latest('date')
            ->paginate(10);

        $totalMes     = Expense::whereMonth('date', date('m'))->whereYear('date', date('Y'))->sum('amount');
        $totalGeneral = Expense::sum('amount');

        return view('livewire.admin.expense-manager', [
            'expenses'     => $expenses,
            'totalMes'     => $totalMes,
            'totalGeneral' => $totalGeneral,
        ])->layout('layouts.app');
    }
}
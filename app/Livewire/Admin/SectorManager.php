<?php 

namespace App\Livewire\Admin;

use App\Models\Sector;
use Livewire\Component;

class SectorManager extends Component
{
    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public $name           = '';
    public $search         = '';
    public $editingId      = null;
    public $confirmingId   = null;
    public $showForm       = false;

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:3',
                'max:100',
                $this->editingId
                    ? 'unique:sectors,name,' . $this->editingId
                    : 'unique:sectors,name'
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre del sector es obligatorio.',
        'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
        'name.max'      => 'El nombre no puede exceder 100 caracteres.',
        'name.unique'   => 'Ya existe un sector con ese nombre.',
    ];

    // =========================================================================
    // WATCHERS
    // =========================================================================

    public function updatedSearch()
    {
        // Resetear filtro
    }

    // =========================================================================
    // ACCIONES
    // =========================================================================

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editSector($id)
    {
        $sector = Sector::findOrFail($id);
        $this->editingId = $id;
        $this->name = $sector->name;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            // Actualizar
            $sector = Sector::findOrFail($this->editingId);
            $sector->update(['name' => $this->name]);
            session()->flash('message', 'Sector actualizado correctamente.');
        } else {
            // Crear
            Sector::create(['name' => $this->name]);
            session()->flash('message', 'Sector añadido con éxito.');
        }

        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingId = null;
    }

    public function delete($id)
    {
        try {
            $sector = Sector::findOrFail($id);
            
            // Verificar si tiene socios asociados
            if ($sector->associates()->exists()) {
                session()->flash('error', 'No se puede eliminar: el sector tiene socios asociados. Reasignalos primero.');
                $this->confirmingId = null;
                return;
            }

            $sector->delete();
            $this->confirmingId = null;
            session()->flash('message', 'Sector eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el sector.');
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function resetForm()
    {
        $this->name = '';
        $this->editingId = null;
        $this->showForm = false;
        $this->resetValidation();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        $sectors = Sector::with('associates')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->get();

        return view('livewire.admin.sector-manager', [
            'sectors' => $sectors
        ])->layout('layouts.app');
    }
}
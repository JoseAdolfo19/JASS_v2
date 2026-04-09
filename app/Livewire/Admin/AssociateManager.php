<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Sector;
use Livewire\Component;
use Livewire\WithPagination;

class AssociateManager extends Component
{
    use WithPagination;

    // =========================================================================
    // PROPIEDADES DE BÚSQUEDA Y FILTROS
    // =========================================================================

    public string $search         = '';
    public string $filterStatus   = '';   // '' | 'activo' | 'suspendido'
    public string $filterSector   = '';   // ID del sector o ''

    // =========================================================================
    // CONTROL DE UI
    // =========================================================================

    public bool $showModal        = false;
    public bool $isEditMode       = false;
    public ?int $confirmingDelete = null;

    // =========================================================================
    // CAMPOS DEL FORMULARIO
    // =========================================================================

    public ?int   $associate_id      = null;
    public string $name              = '';
    public string $last_name         = '';
    public string $dni               = '';
    public string $entry_date        = '';
    public string $sector_id         = '';
    public string $address           = '';
    public string $meter_number      = '';
    public string $address_reference = '';
    public string $status            = 'activo';

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    protected function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'dni'               => 'required|digits:8|unique:associates,dni,' . $this->associate_id,
            'entry_date'        => 'required|date',
            'sector_id'         => 'required|exists:sectors,id',
            'address'           => 'nullable|string|max:500',
            'meter_number'      => 'nullable|string|max:50',
            'address_reference' => 'nullable|string|max:200',
            'status'            => 'required|in:activo,suspendido',
        ];
    }

    protected $messages = [
        'sector_id.required' => 'Debe seleccionar un sector.',
        'sector_id.exists'   => 'El sector seleccionado no existe.',
        'dni.unique'         => 'Este DNI ya pertenece a otro socio.',
        'dni.digits'         => 'El DNI debe tener exactamente 8 dígitos.',
        'name.required'      => 'El nombre es obligatorio.',
        'last_name.required' => 'El apellido es obligatorio.',
        'entry_date.required'=> 'La fecha de inscripción es obligatoria.',
    ];

    // =========================================================================
    // WATCHERS — resetear paginación al cambiar filtros
    // =========================================================================

    public function updatedSearch(): void        { $this->resetPage(); }
    public function updatedFilterStatus(): void  { $this->resetPage(); }
    public function updatedFilterSector(): void  { $this->resetPage(); }

    // =========================================================================
    // ACCIONES DEL MODAL
    // =========================================================================

    public function openModal(): void
    {
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editSocio(int $id): void
    {
        $socio = Associate::findOrFail($id);

        $this->associate_id      = $id;
        $this->name              = $socio->name;
        $this->last_name         = $socio->last_name;
        $this->dni               = $socio->dni;
        $this->entry_date        = $socio->entry_date->format('Y-m-d');
        $this->sector_id         = (string) $socio->sector_id;
        $this->address           = $socio->address ?? '';
        $this->meter_number      = $socio->meter_number ?? '';
        $this->address_reference = $socio->address_reference ?? '';
        $this->status            = $socio->status;

        $this->isEditMode = true;
        $this->showModal  = true;
    }

    public function saveSocio(): void
    {
        $this->validate();

        Associate::updateOrCreate(
            ['id' => $this->associate_id],
            [
                'name'              => $this->name,
                'last_name'         => $this->last_name,
                'dni'               => $this->dni,
                'entry_date'        => $this->entry_date,
                'sector_id'         => $this->sector_id,
                'address'           => $this->address ?: null,
                'meter_number'      => $this->meter_number ?: null,
                'address_reference' => $this->address_reference ?: null,
                'status'            => $this->status,
            ]
        );

        $this->showModal = false;
        session()->flash('message', $this->isEditMode
            ? 'Socio actualizado correctamente.'
            : 'Socio registrado con éxito.'
        );
    }

    // =========================================================================
    // SUSPENDER / ACTIVAR
    // =========================================================================

    public function toggleStatus(int $id): void
    {
        $socio = Associate::findOrFail($id);
        $socio->status = $socio->status === 'activo' ? 'suspendido' : 'activo';
        $socio->save();

        $accion = $socio->status === 'suspendido' ? 'suspendido' : 'reactivado';
        session()->flash('message', "Socio {$accion} correctamente.");
    }

    // =========================================================================
    // ELIMINAR CON CONFIRMACIÓN
    // =========================================================================

    public function confirmDelete(int $id): void
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
    }

    public function eliminarSocio(int $id): void
    {
        try {
            $socio = Associate::findOrFail($id);

            if ($socio->payments()->exists()) {
                session()->flash('error', 'No se puede eliminar: el socio tiene pagos registrados. Usa "Suspender" en su lugar.');
                $this->confirmingDelete = null;
                return;
            }

            $socio->delete();
            $this->confirmingDelete = null;
            session()->flash('message', 'Socio dado de baja del sistema.');

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al eliminar.');
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function resetForm(): void
    {
        $this->associate_id      = null;
        $this->name              = '';
        $this->last_name         = '';
        $this->dni               = '';
        $this->entry_date        = '';
        $this->sector_id         = '';
        $this->address           = '';
        $this->meter_number      = '';
        $this->address_reference = '';
        $this->status            = 'activo';
        $this->isEditMode        = false;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $associates = Associate::with('sector')
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                      ->orWhere('dni', 'like', "%{$this->search}%")
                      ->orWhere('meter_number', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSector, fn($q) => $q->where('sector_id', $this->filterSector))
            ->orderBy('last_name')
            ->paginate(15);

        $sectores = Sector::orderBy('name')->get();

        $totalActivos    = Associate::where('status', 'activo')->count();
        $totalSuspendidos = Associate::where('status', 'suspendido')->count();

        return view('livewire.admin.associate-manager', [
            'associates'       => $associates,
            'sectores'         => $sectores,
            'totalActivos'     => $totalActivos,
            'totalSuspendidos' => $totalSuspendidos,
        ])->layout('layouts.app');
    }
}
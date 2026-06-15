<?php
 
namespace App\Livewire\Admin;
 
use App\Models\Associate;
use App\Models\Connection;
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
    public string $filterStatus   = '';
    public string $filterSector   = '';
    public string $sortBy         = 'last_name';
    public string $sortDirection  = 'asc';
    public string $filterDateFrom = '';
    public string $filterDateTo   = '';
    public int    $perPage        = 15;
 
    // =========================================================================
    // CONTROL DE UI
    // =========================================================================
 
    public bool $showModal        = false;
    public bool $isEditMode       = false;
    public ?int $confirmingDelete = null;
 
    // =========================================================================
    // CAMPOS DEL FORMULARIO SOCIO
    // =========================================================================
 
    public ?int   $associate_id      = null;
    public string $name;
    public string $last_name;
    public string $dni;
    public string $entry_date        = '';
    public string $sector_id         = '';
    public string $address           = '';
    public string $meter_number      = '';
    public string $address_reference = '';
    public string $status            = 'activo';
 
    // =========================================================================
    // RENIEC
    // =========================================================================
 
    public string $reniecEstado   = '';
    public string $reniecMensaje  = '';
 
    // =========================================================================
    // CONEXIONES / INSTALACIONES ADICIONALES
    // =========================================================================
 
    public bool  $showConnectionModal  = false;
    public ?int  $connection_id        = null;
    public ?int  $conn_associate_id    = null;
    public string $conn_label          = '';
    public string $conn_sector_id      = '';
    public string $conn_address        = '';
    public string $conn_meter_number   = '';
    public string $conn_entry_date     = '';
    public string $conn_status         = 'activo';
 
    // =========================================================================
    // PANEL DE CONEXIONES
    // =========================================================================
 
    public ?int $viewingConnectionsOf = null;
    public $socioSeleccionado = null;
    public $conexiones = [];
 
    // =========================================================================
    // RENIEC
    // =========================================================================
 
    public function updatedDni(): void
    {
        $this->reniecEstado  = '';
        $this->reniecMensaje = '';
 
        if (strlen($this->dni) === 8 && ctype_digit($this->dni)) {
            $this->buscarEnReniec();
        }
    }
 
    public function buscarEnReniec()
    {
        $this->reniecEstado = 'cargando';
        $this->reniecMensaje = 'Buscando...';
 
        try {
            $token = config('services.consultaperu.token');
 
            $response = \Illuminate\Support\Facades\Http::timeout(8)
                ->post('https://api.consultaperuapi.com/api/v1/consultas-dni', [
                    'token' => $token,
                    'dni' => $this->dni,
                ]);
 
            if ($response->successful()) {
                $data = $response->json();
 
                if ($data['success'] ?? false) {
                    // Asignar los datos a los campos del formulario
                    $this->name = $data['data']['nombres'] ?? '';
                    $this->last_name = ($data['data']['apellidoPaterno'] ?? '') . ' ' . ($data['data']['apellidoMaterno'] ?? '');
 
                    $this->reniecEstado = 'exito';
                    $this->reniecMensaje = '✓ Datos cargados desde RENIEC';
 
                    // 👇 IMPORTANTE: Forzar actualización de la vista
                    $this->dispatch('reniec-cargado');
                } else {
                    $this->reniecEstado = 'error';
                    $this->reniecMensaje = 'DNI no encontrado en RENIEC';
                }
            }
        } catch (\Exception $e) {
            $this->reniecEstado = 'error';
            $this->reniecMensaje = 'Error de conexión: ' . $e->getMessage();
        }
    }
 
    protected function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'dni'               => 'digits:8|unique:associates,dni,' . $this->associate_id,
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
        'entry_date.required' => 'La fecha de inscripción es obligatoria.',
    ];
 
    // =========================================================================
    // WATCHERS - Resetear página al cambiar filtros
    // =========================================================================
 
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }
    public function updatedFilterSector(): void
    {
        $this->resetPage();
    }
    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
    }
    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
    }
    public function updatedSortBy(): void
    {
        $this->resetPage();
    }
    public function updatedSortDirection(): void
    {
        $this->resetPage();
    }
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
 
    // =========================================================================
    // ORDENAMIENTO
    // =========================================================================
 
    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
 
    // =========================================================================
    // ACCIONES DEL MODAL SOCIO
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
                'address'           => trim($this->address) !== '' ? trim($this->address) : null,
                'meter_number'      => trim($this->meter_number) !== '' ? trim($this->meter_number) : null,
                'address_reference' => trim($this->address_reference) !== '' ? trim($this->address_reference) : null,
                'status'            => $this->status,
            ]
        );
 
        $this->showModal = false;
        $this->resetForm();
        session()->flash(
            'message',
            $this->isEditMode
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
 
        // Limpiar confirmación pendiente
        $this->confirmingDelete = null;
    }
 
    // =========================================================================
    // ELIMINAR SOCIO
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
    // PANEL DE CONEXIONES
    // =========================================================================
 
    public function verConexiones(int $socioId): void
    {
        $this->viewingConnectionsOf = $socioId;
        $this->socioSeleccionado = Associate::with(['connections.sector'])->find($socioId);
 
        if ($this->socioSeleccionado) {
            $this->conexiones = $this->socioSeleccionado->connections()
                ->with('sector')
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $this->conexiones = collect();
        }
    }
 
    public function cerrarConexiones(): void
    {
        $this->viewingConnectionsOf = null;
        $this->socioSeleccionado = null;
        $this->conexiones = [];
    }
 
    public function toggleConexionActiva(int $connectionId): void
    {
        $conn = Connection::findOrFail($connectionId);
 
        if ($conn->is_primary) {
            session()->flash('error', 'No se puede desactivar la conexión principal.');
            return;
        }
 
        $conn->active = !$conn->active;
        $conn->save();
 
        $estado = $conn->active ? 'activada' : 'desactivada';
        session()->flash('message', "Instalación {$estado} correctamente.");
 
        if ($this->viewingConnectionsOf) {
            $this->verConexiones($this->viewingConnectionsOf);
        }
    }
 
    // =========================================================================
    // CRUD CONEXIONES
    // =========================================================================
 
    public function abrirNuevaConexion(?int $associateId = null): void
    {
        if ($associateId === null && $this->viewingConnectionsOf) {
            $associateId = $this->viewingConnectionsOf;
        }
 
        $this->resetConnectionForm();
        $this->conn_associate_id = $associateId;
        $this->showConnectionModal = true;
    }
 
    public function editarConexion(int $connectionId): void
    {
        $conn = Connection::findOrFail($connectionId);
        $this->connection_id      = $conn->id;
        $this->conn_associate_id  = $conn->associate_id;
        $this->conn_label         = $conn->label;
        $this->conn_sector_id     = (string) ($conn->sector_id ?? '');
        $this->conn_address       = $conn->address ?? '';
        $this->conn_meter_number  = $conn->meter_number ?? '';
        $this->conn_entry_date    = $conn->entry_date?->format('Y-m-d') ?? '';
        $this->conn_status        = $conn->status ?? 'activo';
        $this->showConnectionModal = true;
    }
 
    public function guardarConexion(): void
    {
        $this->validate([
            'conn_label'        => 'required|string|max:100',
            'conn_sector_id'    => 'required|exists:sectors,id',
            'conn_entry_date'   => 'required|date',
            'conn_address'      => 'nullable|string|max:500',
            'conn_meter_number' => 'nullable|string|max:50',
            'conn_status'       => 'required|in:activo,suspendido',
        ], [
            'conn_label.required'      => 'El nombre de la instalación es obligatorio.',
            'conn_sector_id.required'  => 'Debe seleccionar el sector.',
            'conn_entry_date.required' => 'La fecha de inicio de cobro es obligatoria.',
        ]);
 
        $datos = [
            'associate_id' => $this->conn_associate_id,
            'sector_id'    => $this->conn_sector_id,
            'label'        => $this->conn_label,
            'is_primary'   => false,
            'address'      => trim($this->conn_address) ?: null,
            'meter_number' => trim($this->conn_meter_number) ?: null,
            'entry_date'   => $this->conn_entry_date,
            'status'       => $this->conn_status,
            'active'       => $this->conn_status === 'activo',
        ];
 
        $esNueva = is_null($this->connection_id);
 
        if ($esNueva) {
            // NUEVA instalación — siempre crear
            Connection::create($datos);
        } else {
            // EDITAR instalación existente
            $conn = Connection::find($this->connection_id);
            if ($conn) {
                $conn->update($datos);
            }
        }
 
        // Crear conexión primaria del socio si aún no existe
        $asociado = Associate::find($this->conn_associate_id);
        if ($asociado && !$asociado->connections()->where('is_primary', true)->exists()) {
            $asociado->connections()->create([
                'sector_id'    => $asociado->sector_id,
                'label'        => 'Conexión Principal',
                'is_primary'   => true,
                'address'      => $asociado->address,
                'meter_number' => $asociado->meter_number,
                'entry_date'   => $asociado->entry_date,
                'status'       => $asociado->status,
                'active'       => true,
            ]);
        }
 
        $mensaje = $esNueva
            ? 'Segunda instalación registrada correctamente.'
            : 'Instalación actualizada correctamente.';
 
        $asociadoId = $this->conn_associate_id;
 
        $this->showConnectionModal = false;
        $this->resetConnectionForm();
 
        if ($this->viewingConnectionsOf) {
            $this->verConexiones($this->viewingConnectionsOf);
        }
 
        session()->flash('message', $mensaje);
    }
 
    public function eliminarConexion(int $connectionId): void
    {
        $conn = Connection::findOrFail($connectionId);
 
        if ($conn->is_primary) {
            session()->flash('error', 'No se puede eliminar la conexión principal.');
            return;
        }
 
        if ($conn->payments()->exists()) {
            session()->flash('error', 'No se puede eliminar: tiene pagos registrados.');
            return;
        }
 
        $conn->delete();
        session()->flash('message', 'Instalación eliminada.');
 
        if ($this->viewingConnectionsOf) {
            $this->verConexiones($this->viewingConnectionsOf);
        }
    }
 
    private function resetConnectionForm(): void
    {
        $this->connection_id      = null;
        $this->conn_associate_id  = null;
        $this->conn_label         = '';
        $this->conn_sector_id     = '';
        $this->conn_address       = '';
        $this->conn_meter_number  = '';
        $this->conn_entry_date    = '';
        $this->conn_status        = 'activo';
    }
 
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
        $this->reniecEstado      = '';
        $this->reniecMensaje     = '';
        $this->confirmingDelete  = null;
    }
 
    // =========================================================================
    // LIMPIAR FILTROS
    // =========================================================================
 
    public function resetFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterSector   = '';
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';
        $this->resetPage();
        session()->flash('message', 'Filtros eliminados correctamente.');
    }
 
    // =========================================================================
    // RENDER - La clave está en ->appends() y mantener los filtros en paginación
    // =========================================================================
 
    public function render(): mixed
    {
        $query = Associate::with('sector')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(
                    fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('dni', 'like', "%{$this->search}%")
                        ->orWhere('meter_number', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSector, fn($q) => $q->where('sector_id', $this->filterSector))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('entry_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('entry_date', '<=', $this->filterDateTo))
            ->orderBy($this->sortBy, $this->sortDirection);
 
        // Paginar Y MANTENER los filtros en los enlaces de página
        $associates = $query->paginate($this->perPage);
 
        // 👇 ESTO es lo que resuelve el problema de paginación con filtros
        $associates->appends([
            'search'         => $this->search,
            'filterStatus'   => $this->filterStatus,
            'filterSector'   => $this->filterSector,
            'filterDateFrom' => $this->filterDateFrom,
            'filterDateTo'   => $this->filterDateTo,
            'sortBy'         => $this->sortBy,
            'sortDirection'  => $this->sortDirection,
            'perPage'        => $this->perPage,
        ]);
 
        $associateIds = $associates->pluck('id');
        $conexionesAdicionales = Connection::with(['associate', 'sector'])
            ->whereIn('associate_id', $associateIds)
            ->where('is_primary', false)
            ->orderBy('associate_id')
            ->get()
            ->groupBy('associate_id');
 
        $sectores = Sector::orderBy('name')->get();
 
        // Contar también las conexiones secundarias (no primarias)
        $connectionsActiveCount = Connection::where('is_primary', false)->where('status', 'activo')->count();
        $connectionsSuspendedCount = Connection::where('is_primary', false)->where('status', 'suspendido')->count();

        $totalActivos     = Associate::where('status', 'activo')->count() + $connectionsActiveCount;
        $totalSuspendidos = Associate::where('status', 'suspendido')->count() + $connectionsSuspendedCount;

        // Contar filas mostradas en la página (socios + instalaciones adicionales de los socios en la página)
        $totalEnPagina = $associates->count() + $conexionesAdicionales->map(fn($g) => $g->count())->sum();

        return view('livewire.admin.associate-manager', [
            'associates'            => $associates,
            'conexionesAdicionales' => $conexionesAdicionales,
            'sectores'              => $sectores,
            'totalActivos'          => $totalActivos,
            'totalSuspendidos'      => $totalSuspendidos,
            'totalEnPagina'         => $totalEnPagina,
        ])->layout('layouts.app');
    }
}
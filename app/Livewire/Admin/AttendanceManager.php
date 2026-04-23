<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceManager extends Component
{
    use WithPagination;

    // =========================================================================
    // VISTA ACTIVA: 'lista' | 'nuevo' | 'pasar_lista'
    // =========================================================================
    public string $vista = 'lista';

    // =========================================================================
    // FORMULARIO NUEVO EVENTO
    // =========================================================================
    public string $type        = 'asamblea';
    public string $title       = '';
    public string $date        = '';
    public string $description = '';

    // =========================================================================
    // PASAR LISTA
    // =========================================================================
    public ?int $event_id        = null;
    public array $asistencias    = []; // [associate_id => 'presente'|'ausente'|'justificado']
    public ?Event $eventoActual  = null;
    public string $search        = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->date = date('Y-m-d');
    }

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    protected function rules(): array
    {
        return [
            'type'        => 'required|in:asamblea,faena',
            'title'       => 'required|string|max:200',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'title.required' => 'El título del evento es obligatorio.',
        'date.required'  => 'La fecha es obligatoria.',
    ];

    // =========================================================================
    // NAVEGACIÓN
    // =========================================================================

    public function irANuevo(): void
    {
        $this->resetForm();
        $this->vista = 'nuevo';
    }

    public function irALista(): void
    {
        $this->vista      = 'lista';
        $this->event_id   = null;
        $this->eventoActual = null;
        $this->asistencias = [];
        $this->resetPage();
    }

    public function abrirPasarLista(int $eventId): void
    {
        $evento = Event::with(['attendances.associate'])->findOrFail($eventId);

        if ($evento->lista_cerrada) {
            session()->flash('error', 'Esta lista ya fue cerrada y no se puede editar.');
            return;
        }

        $this->event_id      = $eventId;
        $this->eventoActual  = $evento;
        $this->asistencias   = [];

        // Cargar asistencias existentes o inicializar con 'ausente'
        $asistenciasGuardadas = $evento->attendances->keyBy('associate_id');

        $socios = Associate::where('status', 'activo')->orderBy('last_name')->get();

        foreach ($socios as $socio) {
            $this->asistencias[$socio->id] = $asistenciasGuardadas->has($socio->id)
                ? $asistenciasGuardadas[$socio->id]->status
                : 'ausente';
        }

        $this->vista = 'pasar_lista';
    }

    // =========================================================================
    // CREAR EVENTO
    // =========================================================================

    public function crearEvento(): void
    {
        $this->validate();

        $tipoLabel = $this->type === 'asamblea' ? 'Asamblea' : 'Faena';
        $titulo    = $this->title ?: "{$tipoLabel} del " . Carbon::parse($this->date)->format('d/m/Y');

        $evento = Event::create([
            'type'        => $this->type,
            'title'       => $titulo,
            'date'        => $this->date,
            'description' => $this->description ?: null,
        ]);

        // Pre-cargar todos los socios activos como 'ausente'
        $socios = Associate::where('status', 'activo')->get();
        foreach ($socios as $socio) {
            EventAttendance::create([
                'event_id'     => $evento->id,
                'associate_id' => $socio->id,
                'status'       => 'ausente',
            ]);
        }

        session()->flash('message', 'Evento creado. Ahora puedes pasar lista.');
        $this->abrirPasarLista($evento->id);
    }

    // =========================================================================
    // MARCAR ASISTENCIA (toggle)
    // =========================================================================

    public function toggleAsistencia(int $associateId): void
    {
        $actual = $this->asistencias[$associateId] ?? 'ausente';

        // Ciclo: ausente → presente → justificado → ausente
        $siguiente = match($actual) {
            'ausente'     => 'presente',
            'presente'    => 'justificado',
            'justificado' => 'ausente',
            default       => 'presente',
        };

        $this->asistencias[$associateId] = $siguiente;
        $this->guardarAsistenciaIndividual($associateId, $siguiente);
    }

    public function marcarTodos(string $status): void
    {
        foreach ($this->asistencias as $id => $v) {
            $this->asistencias[$id] = $status;
            $this->guardarAsistenciaIndividual($id, $status);
        }
    }

    private function guardarAsistenciaIndividual(int $associateId, string $status): void
    {
        if (!$this->event_id) {
            return;
        }

        EventAttendance::updateOrCreate(
            ['event_id' => $this->event_id, 'associate_id' => $associateId],
            ['status' => $status]
        );
    }

    // =========================================================================
    // GUARDAR LISTA + MULTAS AUTOMÁTICAS
    // =========================================================================

    public function guardarLista(): void
    {
        if (!$this->event_id) return;

        $evento = Event::findOrFail($this->event_id);

        // Guardar cada asistencia
        foreach ($this->asistencias as $associateId => $status) {
            EventAttendance::updateOrCreate(
                ['event_id' => $this->event_id, 'associate_id' => $associateId],
                ['status' => $status]
            );
        }

        session()->flash('message', 'Lista guardada correctamente.');
    }

    public function cerrarListaYMultar(): void
    {
        if (!$this->event_id) return;

        $evento = Event::findOrFail($this->event_id);

        if ($evento->lista_cerrada) {
            session()->flash('error', 'Esta lista ya fue cerrada anteriormente.');
            return;
        }

        // Guardar asistencias
        foreach ($this->asistencias as $associateId => $status) {
            EventAttendance::updateOrCreate(
                ['event_id' => $this->event_id, 'associate_id' => $associateId],
                ['status' => $status]
            );
        }

        // Guardar la lista y cerrar el evento.
        $evento->update(['lista_cerrada' => true]);

        $ausentes = collect($this->asistencias)->filter(fn($s) => $s === 'ausente');
        $totalMultados = $ausentes->count();

        session()->flash('message', "Lista cerrada. {$totalMultados} falta(s) serán cobradas como multa de S/ 60 en la próxima boleta de cuota familiar.");
        $this->irALista();
    }

    // =========================================================================
    // EXPORTAR PDF
    // =========================================================================

    public function exportarPDF(int $eventId): mixed
    {
        $evento = Event::with(['attendances' => function ($q) {
            $q->with('associate.sector')->orderBy('status');
        }])->findOrFail($eventId);

        $jass = [
            'nombre'    => Setting::get('jass_nombre', 'JASS'),
            'direccion' => Setting::get('jass_direccion', ''),
        ];

        return response()->streamDownload(function () use ($evento, $jass) {
            $pdf = Pdf::loadView('pdf.asistencia', compact('evento', 'jass'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->getDomPDF()->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, 'asistencia-' . $evento->date->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // ELIMINAR EVENTO
    // =========================================================================

    public ?int $confirmingDelete = null;

    public function confirmDelete(int $id): void  { $this->confirmingDelete = $id; }
    public function cancelDelete(): void           { $this->confirmingDelete = null; }

    public function eliminarEvento(int $id): void
    {
        $evento = Event::findOrFail($id);
        if ($evento->lista_cerrada) {
            session()->flash('error', 'No se puede eliminar un evento con lista cerrada.');
            $this->confirmingDelete = null;
            return;
        }
        $evento->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Evento eliminado.');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function resetForm(): void
    {
        $this->type        = 'asamblea';
        $this->title       = '';
        $this->date        = date('Y-m-d');
        $this->description = '';
        $this->resetValidation();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $eventos = Event::withCount([
                'attendances as presentes_count' => fn($q) => $q->where('status', 'presente'),
                'attendances as ausentes_count'  => fn($q) => $q->where('status', 'ausente'),
                'attendances as total_count',
            ])
            ->orderByDesc('date')
            ->paginate(10);

        $socios = $this->vista === 'pasar_lista'
            ? Associate::where('status', 'activo')
                ->when($this->search, fn($query) => $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('dni', 'like', "%{$this->search}%");
                }))
                ->orderBy('last_name')
                ->get()
            : collect();

        return view('livewire.admin.attendance-manager', [
            'eventos' => $eventos,
            'socios'  => $socios,
        ])->layout('layouts.app');
    }
}
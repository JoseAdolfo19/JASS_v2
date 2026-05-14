<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Event;
use App\Models\EventAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceManager extends Component
{
    use WithPagination;

    protected $layout = 'layouts.app';

    // ── Navegación ──────────────────────────────────────────────────────────
    public string $vista = 'lista';   // lista | nuevo | pasar_lista

    // ── Formulario nuevo evento ──────────────────────────────────────────────
    public string $type        = 'asamblea';
    public string $title       = '';
    public string $date        = '';
    public string $description = '';

    // ── Pasar lista ─────────────────────────────────────────────────────────
    public ?int $eventoActualId    = null;
    public array $asistencias      = [];   // [associate_id => 'presente'|'ausente'|'justificado']
    public string $search          = '';
    public int $confirmingDelete   = 0;

    // ── Helpers privados (sin caché, calculados fresh en cada render) ───────

    // =========================================================================
    // NAVEGACIÓN
    // =========================================================================

    public function irALista(): void
    {
        $this->reset(['vista', 'eventoActualId', 'asistencias', 'search',
                      'type', 'title', 'date', 'description']);
        $this->vista = 'lista';
        $this->resetPage();
    }

    public function irANuevo(): void
    {
        $this->reset(['type', 'title', 'date', 'description']);
        $this->vista = 'nuevo';
    }

    // =========================================================================
    // EVENTOS — CRUD
    // =========================================================================

    protected array $rules = [
        'type'  => 'required|in:asamblea,faena',
        'title' => 'required|string|max:255',
        'date'  => 'required|date',
    ];

    public function crearEvento(): void
    {
        $this->validate();

        $evento = Event::create([
            'type'        => $this->type,
            'title'       => $this->title,
            'date'        => $this->date,
            'description' => $this->description ?: null,
            'lista_cerrada' => false,
        ]);

        $this->abrirPasarLista($evento->id);
    }

    public function confirmDelete(int $id): void  { $this->confirmingDelete = $id; }
    public function cancelDelete(): void           { $this->confirmingDelete = 0; }

    public function eliminarEvento(int $id): void
    {
        $evento = Event::find($id);

        if ($evento && !$evento->lista_cerrada) {
            $evento->attendances()->delete();
            $evento->delete();
            session()->flash('message', 'Evento eliminado correctamente.');
        }

        $this->confirmingDelete = 0;
    }

    // =========================================================================
    // PASAR LISTA
    // =========================================================================

    public function abrirPasarLista(int $eventoId): void
    {
        $this->eventoActualId = $eventoId;
        $this->search         = '';
        $this->vista          = 'pasar_lista';

        // Las claves se castean a string porque Livewire serializa arrays
        // con claves string. Sin esto, (int)123 !== (string)"123" y
        // toggleAsistencia nunca encontraría el estado guardado.
        $this->asistencias = EventAttendance::where('event_id', $eventoId)
            ->pluck('status', 'associate_id')
            ->mapWithKeys(fn($status, $id) => [(string) $id => $status])
            ->toArray();
    }

    /**
     * Marcar asistencia como presente
     */
    public function marcarPresente(int $asociadoId): void
    {
        $this->actualizarAsistencia($asociadoId, 'presente');
    }

    /**
     * Marcar asistencia como justificado
     */
    public function marcarJustificado(int $asociadoId): void
    {
        $this->actualizarAsistencia($asociadoId, 'justificado');
    }

    /**
     * Marcar asistencia como ausente
     */
    public function marcarAusente(int $asociadoId): void
    {
        $this->actualizarAsistencia($asociadoId, 'ausente');
    }

    /**
     * Método privado para actualizar asistencia
     */
    private function actualizarAsistencia(int $asociadoId, string $status): void
    {
        $key = (string) $asociadoId;
        $this->asistencias[$key] = $status;

        EventAttendance::updateOrCreate(
            ['event_id' => $this->eventoActualId, 'associate_id' => $asociadoId],
            ['status' => $status, 'fine_paid' => false]
        );
    }

    // =========================================================================
    // CERRAR LISTA
    // =========================================================================

    public function cerrarListaYMultar(): void
    {
        $evento = $this->eventoActualId ? Event::find($this->eventoActualId) : null;
        if (!$evento || $evento->lista_cerrada) return;

        // Asegurarse de que todos los socios activos tienen registro
        $todosLosIds = Associate::where('status', 'activo')->pluck('id');

        $registros = $todosLosIds->map(fn($id) => [
            'event_id'     => $evento->id,
            'associate_id' => $id,
            'status'       => $this->asistencias[(string) $id] ?? 'ausente',
            'fine_paid'    => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ])->toArray();

        // Upsert masivo — más eficiente que N queries individuales
        EventAttendance::upsert(
            $registros,
            ['event_id', 'associate_id'],
            ['status', 'updated_at']
        );

        $evento->update(['lista_cerrada' => true]);

        session()->flash('message', 'Lista cerrada. Los ausentes tienen multa pendiente.');
        $this->irALista();
    }

    // =========================================================================
    // EXPORTAR PDF
    // =========================================================================

    public function exportarPDF(int $eventoId): mixed
    {
        $evento = Event::with(['attendances.associate.sector'])->find($eventoId);
        if (!$evento) return null;

        $asistencias = $evento->attendances->groupBy('status');

        $data = [
            'evento'       => $evento,
            'presentes'    => $asistencias->get('presente',    collect()),
            'justificados' => $asistencias->get('justificado', collect()),
            'ausentes'     => $asistencias->get('ausente',     collect()),
        ];

        return response()->streamDownload(function () use ($data) {
            echo Pdf::loadView('pdf.asistencia', $data)
                ->setPaper('a4', 'portrait')
                ->output();
        }, 'asistencia-' . $evento->id . '.pdf');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        // Eventos paginados para la vista lista
        $eventos = Event::withCount([
                'attendances as presentes_count'  => fn($q) => $q->where('status', 'presente'),
                'attendances as ausentes_count'   => fn($q) => $q->where('status', 'ausente'),
                'attendances as total_count',
            ])
            ->latest('date')
            ->paginate(15);

        // Evento actual (calculado aquí, no como computed property cacheada)
        $eventoActual = $this->eventoActualId
            ? Event::find($this->eventoActualId)
            : null;

        // Socios filtrados (calculado aquí para que sea fresh en cada request)
        $query = Associate::where('status', 'activo')->orderBy('last_name');
        if (strlen($this->search) >= 2) {
            $query->where(fn($q) =>
                $q->where('name',      'like', "%{$this->search}%")
                  ->orWhere('last_name','like', "%{$this->search}%")
                  ->orWhere('dni',      'like', "%{$this->search}%")
            );
        }
        $socios = $query->with('sector')->get();

        // Contadores calculados desde el array de asistencias en memoria
        $asistenciasCol = collect($this->asistencias);
        $conteo = [
            'presentes'    => $asistenciasCol->filter(fn($s) => $s === 'presente')->count(),
            'justificados' => $asistenciasCol->filter(fn($s) => $s === 'justificado')->count(),
            'ausentes'     => $asistenciasCol->filter(fn($s) => $s === 'ausente')->count(),
        ];

        return view('livewire.admin.attendance-manager', [
            'eventos'      => $eventos,
            'eventoActual' => $eventoActual,
            'socios'       => $socios,
            'conteo'       => $conteo,
        ]);
    }
}
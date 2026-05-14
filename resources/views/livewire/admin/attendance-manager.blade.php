<div class="max-w-7xl mx-auto py-8 px-4">

    {{-- ── FLASH ── --}}
    @if(session('message'))
        <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-2xl text-sm font-bold">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-2xl text-sm font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- ================================================================== --}}
    {{-- LISTA DE EVENTOS                                                    --}}
    {{-- ================================================================== --}}
    @if($vista === 'lista')

        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-white font-black uppercase italic text-2xl">Asambleas y Faenas</h2>
                <p class="text-zinc-500 text-xs font-bold mt-1">Control de asistencia y multas automáticas</p>
            </div>
            <button wire:click="irANuevo"
                class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl uppercase text-xs flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Evento
            </button>
        </div>

        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
            <div class="divide-y divide-zinc-800">
                @forelse($eventos as $evento)
                    <div class="p-5 flex items-center gap-4 flex-wrap">

                        {{-- Tipo --}}
                        @if($evento->type === 'asamblea')
                            <span class="bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[10px] font-black px-3 py-1.5 rounded-full uppercase flex-shrink-0">
                                🏛️ Asamblea
                            </span>
                        @else
                            <span class="bg-orange-500/10 border border-orange-500/30 text-orange-400 text-[10px] font-black px-3 py-1.5 rounded-full uppercase flex-shrink-0">
                                ⛏️ Faena
                            </span>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-white font-bold text-sm">{{ $evento->title }}</div>
                            <div class="text-zinc-500 text-xs mt-0.5">{{ $evento->date->format('d/m/Y') }}</div>
                            @if($evento->description)
                                <div class="text-zinc-600 text-xs italic mt-0.5">{{ $evento->description }}</div>
                            @endif
                        </div>

                        {{-- Conteos --}}
                        <div class="flex gap-4 text-center flex-shrink-0">
                            @foreach([['presentes_count','green'],['ausentes_count','red'],['total_count','zinc']] as [$campo, $color])
                                <div>
                                    <div class="text-{{ $color }}-400 font-black text-lg">{{ $evento->$campo }}</div>
                                    <div class="text-zinc-600 text-[9px] uppercase">
                                        {{ $campo === 'presentes_count' ? 'Presentes' : ($campo === 'ausentes_count' ? 'Ausentes' : 'Total') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Estado --}}
                        <div class="flex-shrink-0">
                            @if($evento->lista_cerrada)
                                <span class="bg-zinc-800 text-zinc-500 text-[10px] font-black px-3 py-1.5 rounded-full uppercase">
                                    🔒 Cerrada
                                </span>
                            @else
                                <span class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-[10px] font-black px-3 py-1.5 rounded-full uppercase">
                                    ✏️ Abierta
                                </span>
                            @endif
                        </div>

                        {{-- Acciones --}}
                        <div class="flex gap-2 flex-shrink-0">
                            @unless($evento->lista_cerrada)
                                <button wire:click="abrirPasarLista({{ $evento->id }})"
                                    class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-black px-3 py-2 rounded-xl transition-colors">
                                    Pasar Lista
                                </button>
                            @endunless

                            <button wire:click="exportarPDF({{ $evento->id }})"
                                class="p-2 bg-zinc-800 text-zinc-300 rounded-xl hover:bg-zinc-700 transition-colors" title="Exportar PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>

                            @unless($evento->lista_cerrada)
                                @if($confirmingDelete === $evento->id)
                                    <div class="flex gap-1">
                                        <button wire:click="eliminarEvento({{ $evento->id }})"
                                            class="bg-red-600 text-white text-[10px] font-black px-2 py-1.5 rounded-lg">Sí</button>
                                        <button wire:click="cancelDelete"
                                            class="bg-zinc-700 text-white text-[10px] font-black px-2 py-1.5 rounded-lg">No</button>
                                    </div>
                                @else
                                    <button wire:click="confirmDelete({{ $evento->id }})"
                                        class="p-2 bg-zinc-800 text-red-400 rounded-xl hover:bg-red-600 hover:text-white transition-colors" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            @endunless
                        </div>

                    </div>
                @empty
                    <div class="p-12 text-center text-zinc-600 italic">
                        No hay eventos registrados aún. ¡Crea el primero!
                    </div>
                @endforelse
            </div>

            @if($eventos->hasPages())
                <div class="p-4 border-t border-zinc-800">{{ $eventos->links() }}</div>
            @endif
        </div>

    @endif

    {{-- ================================================================== --}}
    {{-- NUEVO EVENTO                                                        --}}
    {{-- ================================================================== --}}
    @if($vista === 'nuevo')

        <div class="max-w-xl mx-auto">
            <button wire:click="irALista"
                class="text-zinc-500 hover:text-white text-xs font-bold mb-6 flex items-center gap-1 transition-colors">
                ← Volver a la lista
            </button>

            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 shadow-2xl">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic text-lg">Nuevo Evento</h3>
                    <p class="text-zinc-500 text-[10px] font-bold">Asamblea o Faena comunal</p>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Tipo --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-2">Tipo de Evento *</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['asamblea' => ['🏛️','blue'], 'faena' => ['⛏️','orange']] as $valor => [$emoji, $color])
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="type" value="{{ $valor }}" class="sr-only peer">
                                    <div class="p-4 rounded-2xl border-2 border-zinc-700
                                        peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-500/10
                                        text-center transition-all">
                                        <div class="text-2xl mb-1">{{ $emoji }}</div>
                                        <div class="text-white font-black text-sm uppercase">{{ ucfirst($valor) }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('type') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Título --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Título *</label>
                        <input wire:model="title" type="text"
                            placeholder="Ej: Asamblea Ordinaria Abril 2026"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('title') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha *</label>
                        <input wire:model="date" type="date"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Descripción (opcional)</label>
                        <textarea wire:model="description" rows="2"
                            placeholder="Ej: Revisión de tarifas y elección de junta directiva"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                    </div>

                    <button wire:click="crearEvento" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3 rounded-xl uppercase text-sm transition-colors">
                        <span wire:loading.remove wire:target="crearEvento">✅ Crear Evento y Pasar Lista</span>
                        <span wire:loading wire:target="crearEvento">Creando...</span>
                    </button>
                </div>
            </div>
        </div>

    @endif

    {{-- ================================================================== --}}
    {{-- PASAR LISTA                                                         --}}
    {{-- ================================================================== --}}
    @if($vista === 'pasar_lista' && $eventoActual)

        @php
            $estadoConfig = [
                'presente'    => ['label' => '✅ Presente',    'class' => 'bg-green-500/10 border-green-500 text-green-400'],
                'justificado' => ['label' => '🟡 Justificado', 'class' => 'bg-yellow-500/10 border-yellow-500 text-yellow-400'],
                'ausente'     => ['label' => '❌ Ausente',     'class' => 'bg-red-500/10 border-red-500 text-red-400'],
            ];
        @endphp

        <button wire:click="irALista"
            class="text-zinc-500 hover:text-white text-xs font-bold mb-6 flex items-center gap-1 transition-colors">
            ← Volver a la lista
        </button>

        {{-- Cabecera del evento --}}
        <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-5 mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                @if($eventoActual->type === 'asamblea')
                    <span class="bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[10px] font-black px-2 py-1 rounded-full uppercase">
                        🏛️ Asamblea
                    </span>
                @else
                    <span class="bg-orange-500/10 border border-orange-500/30 text-orange-400 text-[10px] font-black px-2 py-1 rounded-full uppercase">
                        ⛏️ Faena
                    </span>
                @endif
                <div class="text-white font-black text-lg mt-1">{{ $eventoActual->title }}</div>
                <div class="text-zinc-500 text-xs">{{ $eventoActual->date->format('d/m/Y') }}</div>
            </div>

            {{-- Contadores reactivos --}}
            <div class="flex gap-4 text-center">
                @foreach([['presentes','green','Presentes'],['justificados','yellow','Justific.'],['ausentes','red','Ausentes']] as [$key,$color,$label])
                    <div>
                        <div class="text-{{ $color }}-400 font-black text-xl">{{ $conteo[$key] }}</div>
                        <div class="text-zinc-600 text-[9px] uppercase">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Buscador --}}
        <div class="mb-4">
            <label class="block text-zinc-400 text-xs uppercase tracking-wide mb-2">Filtrar socio</label>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Buscar por nombre, apellido o DNI..."
                class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
        </div>

        <p class="text-zinc-500 text-xs italic mb-4">
            Haz click en el botón correspondiente para marcar la asistencia. La asistencia se guarda automáticamente en la base de datos.
        </p>

        {{-- Lista de socios --}}
        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl mb-6">
            <div class="divide-y divide-zinc-800">
                @forelse($socios as $socio)
                    @php
                        $estado = $asistencias[(string) $socio->id] ?? 'ausente';
                        $cfg    = $estadoConfig[$estado];
                    @endphp
                    <div class="px-6 py-3 flex items-center justify-between gap-4" wire:key="socio-{{ $socio->id }}">
                        <div class="min-w-0">
                            <div class="text-white font-bold text-sm uppercase truncate">
                                {{ $socio->last_name }}, {{ $socio->name }}
                            </div>
                            <div class="text-zinc-500 text-xs">
                                {{ $socio->sector->name ?? 'Sin sector' }} · DNI {{ $socio->dni }}
                            </div>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            @foreach(['presente' => ['P', 'green'], 'justificado' => ['J', 'yellow'], 'ausente' => ['A', 'red']] as $status => [$label, $color])
                                <button wire:click="marcar{{ ucfirst($status) }}({{ $socio->id }})" wire:key="btn-{{ $status }}-{{ $socio->id }}"
                                    class="font-black text-xs px-2 py-1 rounded-lg border transition-all
                                        @if(($asistencias[(string) $socio->id] ?? 'ausente') === $status)
                                            bg-{{ $color }}-500/20 border-{{ $color }}-500 text-{{ $color }}-400
                                        @else
                                            bg-zinc-700 border-zinc-600 text-zinc-400 hover:bg-zinc-600
                                        @endif">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-600 italic text-sm">
                        No se encontraron socios con ese criterio.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Cerrar lista --}}
        <div class="bg-red-900/10 border border-red-600/20 rounded-2xl p-5">
            <div class="text-red-400 font-black text-sm mb-1">⚠️ Cerrar Lista y Aplicar Multas</div>
            <p class="text-zinc-400 text-xs mb-4">
                Al cerrar, los socios <strong class="text-red-400">Ausentes</strong> recibirán multa automática.
                Los <strong class="text-yellow-400">Justificados</strong> no serán multados.
                Esta acción <strong>no se puede deshacer</strong>.
            </p>
            <button wire:click="cerrarListaYMultar" wire:loading.attr="disabled"
                class="bg-red-600 hover:bg-red-500 disabled:opacity-50 text-white font-black px-6 py-3 rounded-xl uppercase text-sm transition-colors">
                <span wire:loading.remove wire:target="cerrarListaYMultar">🔒 Cerrar Lista y Multar Ausentes</span>
                <span wire:loading wire:target="cerrarListaYMultar">Procesando...</span>
            </button>
        </div>

    @endif

</div>
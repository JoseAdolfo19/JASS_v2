<div class="max-w-7xl mx-auto py-8 px-4">

    {{-- FLASH --}}
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

    {{-- ====================================================================== --}}
    {{-- VISTA: LISTA DE EVENTOS --}}
    {{-- ====================================================================== --}}
    @if($vista === 'lista')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-white font-black uppercase italic text-2xl">Asambleas y Faenas</h2>
            <p class="text-zinc-500 text-xs font-bold mt-1">Control de asistencia y multas automáticas</p>
        </div>
        <button wire:click="irANuevo"
            class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl uppercase text-xs flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Evento
        </button>
    </div>

    <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
        <div class="divide-y divide-zinc-800">
            @forelse($eventos as $evento)
            <div class="p-5 flex items-center gap-4">

                {{-- Tipo badge --}}
                <div class="flex-shrink-0">
                    @if($evento->type === 'asamblea')
                    <span class="bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[10px] font-black px-3 py-1.5 rounded-full uppercase">
                        🏛️ Asamblea
                    </span>
                    @else
                    <span class="bg-orange-500/10 border border-orange-500/30 text-orange-400 text-[10px] font-black px-3 py-1.5 rounded-full uppercase">
                        ⛏️ Faena
                    </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="text-white font-bold text-sm">{{ $evento->title }}</div>
                    <div class="text-zinc-500 text-xs mt-0.5">{{ $evento->date->format('d/m/Y') }}</div>
                    @if($evento->description)
                    <div class="text-zinc-600 text-xs italic mt-0.5">{{ $evento->description }}</div>
                    @endif
                </div>

                {{-- Estadísticas --}}
                <div class="flex gap-4 text-center flex-shrink-0">
                    <div>
                        <div class="text-green-400 font-black text-lg">{{ $evento->presentes_count }}</div>
                        <div class="text-zinc-600 text-[9px] uppercase">Presentes</div>
                    </div>
                    <div>
                        <div class="text-red-400 font-black text-lg">{{ $evento->ausentes_count }}</div>
                        <div class="text-zinc-600 text-[9px] uppercase">Ausentes</div>
                    </div>
                    <div>
                        <div class="text-zinc-400 font-black text-lg">{{ $evento->total_count }}</div>
                        <div class="text-zinc-600 text-[9px] uppercase">Total</div>
                    </div>
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

                    {{-- Pasar lista (solo si no está cerrada) --}}
                    @if(!$evento->lista_cerrada)
                    <button wire:click="abrirPasarLista({{ $evento->id }})"
                        class="p-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500 transition-colors text-xs font-black px-3">
                        Pasar Lista
                    </button>
                    @endif

                    {{-- Exportar PDF --}}
                    <button wire:click="exportarPDF({{ $evento->id }})"
                        class="p-2 bg-zinc-800 text-zinc-300 rounded-xl hover:bg-zinc-700 transition-colors" title="Exportar PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </button>

                    {{-- Eliminar (solo si no está cerrada) --}}
                    @if(!$evento->lista_cerrada)
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    @endif
                    @endif

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

    {{-- ====================================================================== --}}
    {{-- VISTA: NUEVO EVENTO --}}
    {{-- ====================================================================== --}}
    @if($vista === 'nuevo')

    <div class="max-w-xl mx-auto">
        <button wire:click="irALista" class="text-zinc-500 hover:text-white text-xs font-bold mb-6 flex items-center gap-1 transition-colors">
            ← Volver a la lista
        </button>

        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-zinc-800">
                <h3 class="text-white font-black uppercase italic text-lg">Nuevo Evento</h3>
                <p class="text-zinc-500 text-[10px] font-bold">Asamblea o Faena comunal</p>
            </div>
            <div class="p-6 space-y-5">

                {{-- Tipo --}}
                <div>
                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-2">Tipo de Evento *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="asamblea" class="sr-only peer">
                            <div class="p-4 rounded-2xl border-2 border-zinc-700 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 text-center transition-all">
                                <div class="text-2xl mb-1">🏛️</div>
                                <div class="text-white font-black text-sm uppercase">Asamblea</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="faena" class="sr-only peer">
                            <div class="p-4 rounded-2xl border-2 border-zinc-700 peer-checked:border-orange-500 peer-checked:bg-orange-500/10 text-center transition-all">
                                <div class="text-2xl mb-1">⛏️</div>
                                <div class="text-white font-black text-sm uppercase">Faena</div>
                            </div>
                        </label>
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

    {{-- ====================================================================== --}}
    {{-- VISTA: PASAR LISTA --}}
    {{-- ====================================================================== --}}
    @if($vista === 'pasar_lista' && $eventoActual)

    <div>
        <button wire:click="irALista" class="text-zinc-500 hover:text-white text-xs font-bold mb-6 flex items-center gap-1 transition-colors">
            ← Volver a la lista
        </button>

        {{-- Cabecera del evento --}}
        <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-5 mb-6 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($eventoActual->type === 'asamblea')
                    <span class="bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[10px] font-black px-2 py-1 rounded-full uppercase">🏛️ Asamblea</span>
                    @else
                    <span class="bg-orange-500/10 border border-orange-500/30 text-orange-400 text-[10px] font-black px-2 py-1 rounded-full uppercase">⛏️ Faena</span>
                    @endif
                </div>
                <div class="text-white font-black text-lg">{{ $eventoActual->title }}</div>
                <div class="text-zinc-500 text-xs">{{ $eventoActual->date->format('d/m/Y') }}</div>
            </div>
            <div class="flex gap-3 text-center">
                <div>
                    <div class="text-green-400 font-black text-xl">
                        {{ collect($asistencias)->filter(fn($s) => $s === 'presente')->count() }}
                    </div>
                    <div class="text-zinc-600 text-[9px] uppercase">Presentes</div>
                </div>
                <div>
                    <div class="text-yellow-400 font-black text-xl">
                        {{ collect($asistencias)->filter(fn($s) => $s === 'justificado')->count() }}
                    </div>
                    <div class="text-zinc-600 text-[9px] uppercase">Justific.</div>
                </div>
                <div>
                    <div class="text-red-400 font-black text-xl">
                        {{ collect($asistencias)->filter(fn($s) => $s === 'ausente')->count() }}
                    </div>
                    <div class="text-zinc-600 text-[9px] uppercase">Ausentes</div>
                </div>
            </div>
        </div>

        {{-- Buscador de socios --}}
        <div class="mb-4 grid gap-3 md:grid-cols-[1fr_auto] items-end">
            <div>
                <label class="block text-zinc-400 text-xs uppercase tracking-wide mb-2">Buscar socio</label>
                <div class="relative">
                    <input wire:model.live="search" type="text"
                        placeholder="Buscar por nombre, apellido o DNI..."
                        class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500" />
                    @if(!empty($associates))
                    <div class="mb-6 space-y-2">
                        @foreach($associates as $socio)
                        <button wire:click="seleccionarSocio({{ $socio->id }})" class="w-full text-left p-4 bg-zinc-800/50 hover:bg-blue-600/20 rounded-xl border border-zinc-700 transition-all uppercase font-black text-white text-xs">
                            {{ $socio->last_name }}, {{ $socio->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500 text-[11px] uppercase">Buscar</span>
                </div>
            </div>
            <!-- {{-- Botones de acción masiva --}}
            <div class="flex gap-3 mb-4">
                <button wire:click="marcarTodos('presente')"
                    class="bg-green-600/20 border border-green-600/30 text-green-400 text-xs font-black px-4 py-2 rounded-xl hover:bg-green-600/30 transition-colors">
                    ✅ Todos Presentes
                </button>
                <button wire:click="marcarTodos('ausente')"
                    class="bg-red-600/20 border border-red-600/30 text-red-400 text-xs font-black px-4 py-2 rounded-xl hover:bg-red-600/30 transition-colors">
                    ❌ Todos Ausentes
                </button>
                <button wire:click="guardarLista"
                    class="bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-black px-4 py-2 rounded-xl transition-colors ml-auto">
                    💾 Guardar borrador
                </button>
            </div> -->

        </div>
        <div class="text-zinc-400 text-xs italic">
            Si no marcas asistencia, se considerará automáticamente como <strong class="text-red-400">Ausente</strong>.
        </div>


        {{-- Lista de socios --}}
        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl mb-6">
            <div class="divide-y divide-zinc-800">
                @foreach($socios as $socio)
                @php $estado = $asistencias[$socio->id] ?? 'ausente'; @endphp
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <div class="text-white font-bold text-sm uppercase">{{ $socio->last_name }}, {{ $socio->name }}</div>
                        <div class="text-zinc-500 text-xs">{{ $socio->sector->name ?? 'Sin sector' }} · DNI {{ $socio->dni }}</div>
                    </div>
                    <button wire:click="toggleAsistencia({{ $socio->id }})"
                        class="font-black text-xs px-4 py-2 rounded-xl border-2 transition-all
                        {{ $estado === 'presente'    ? 'bg-green-500/10 border-green-500 text-green-400' : '' }}
                        {{ $estado === 'ausente'     ? 'bg-red-500/10 border-red-500 text-red-400' : '' }}
                        {{ $estado === 'justificado' ? 'bg-yellow-500/10 border-yellow-500 text-yellow-400' : '' }}
                        ">
                        @if($estado === 'presente') ✅ Presente
                        @elseif($estado === 'justificado') 🟡 Justificado
                        @else ❌ Ausente
                        @endif
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Cerrar lista --}}
        <div class="bg-red-900/10 border border-red-600/20 rounded-2xl p-5">
            <div class="text-red-400 font-black text-sm mb-1">⚠️ Cerrar Lista y Aplicar Multas</div>
            <p class="text-zinc-400 text-xs mb-4">
                Al cerrar, los socios marcados como <strong class="text-red-400">Ausente</strong> recibirán una multa automática.
                Los <strong class="text-yellow-400">Justificados</strong> no serán multados.
                Esta acción <strong>no se puede deshacer</strong>.
            </p>
            <button wire:click="cerrarListaYMultar" wire:loading.attr="disabled"
                class="bg-red-600 hover:bg-red-500 disabled:opacity-50 text-white font-black px-6 py-3 rounded-xl uppercase text-sm transition-colors">
                <span wire:loading.remove wire:target="cerrarListaYMultar">🔒 Cerrar Lista y Multar Ausentes</span>
                <span wire:loading wire:target="cerrarListaYMultar">Procesando...</span>
            </button>
        </div>
    </div>

    @endif

</div>
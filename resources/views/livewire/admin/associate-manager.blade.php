<div class="max-w-7xl mx-auto py-8 px-4">

    {{-- FLASH MESSAGES --}}
    @if(session('message'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-2xl text-sm font-bold flex justify-between items-center">
        <span>{{ session('message') }}</span>
        <button wire:click="$refresh" class="text-green-500 hover:text-white font-black text-lg leading-none">×</button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-2xl text-sm font-bold">
        {{ session('error') }}
    </div>
    @endif

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
        <div>
            <h2 class="text-white font-black uppercase italic text-2xl">Padrón General de Socios</h2>
            <p class="text-zinc-500 text-xs font-bold mt-1">
                Total: <span class="text-blue-400">{{ $totalActivos + $totalSuspendidos }}</span> socios registrados
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center">
                <div class="text-green-400 text-xl font-black">{{ $totalActivos }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">Activos</div>
            </div>
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center">
                <div class="text-yellow-400 text-xl font-black">{{ $totalSuspendidos }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">Suspendidos</div>
            </div>
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center">
                <div class="text-blue-400 text-xl font-black">{{ $totalEnPagina }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">En página</div>
            </div>
            <button wire:click="openModal"
                class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl uppercase text-xs flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Socio
            </button>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-4 mb-6">
        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Buscar por nombre, apellido, DNI o N° de medidor..."
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <select wire:model.live="filterStatus"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
                <option value="">Todos estados</option>
                <option value="activo">Activos</option>
                <option value="suspendido">Suspendidos</option>
            </select>
            <select wire:model.live="filterSector"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
                <option value="">Todos sectores</option>
                @foreach($sectores as $sector)
                <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                @endforeach
            </select>
            <div>
                <label class="text-zinc-600 text-[10px] font-bold uppercase block mb-1">Desde</label>
                <input wire:model.live="filterDateFrom" type="date"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="text-zinc-600 text-[10px] font-bold uppercase block mb-1">Hasta</label>
                <input wire:model.live="filterDateTo" type="date"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
            </div>
            @if($search || $filterStatus || $filterSector || $filterDateFrom || $filterDateTo)
            <button wire:click="resetFilters"
                class="bg-red-600/20 hover:bg-red-600/40 border border-red-600/50 text-red-400 rounded-xl px-3 py-2 text-xs font-bold transition-colors">
                Limpiar
            </button>
            @endif
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">

        {{-- Control paginación --}}
        <div class="bg-zinc-800/30 border-b border-zinc-800 px-6 py-3 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3">
                <span class="text-zinc-400">Mostrar</span>
                <select wire:model.live="perPage"
                    class="bg-zinc-800 border border-zinc-700 text-white rounded-lg px-3 py-1.5 focus:outline-none focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="15" selected>15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-zinc-400">registros por página</span>
            </div>
            @php
            $current = $associates->currentPage();
            $last = $associates->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
            if ($end - $start < 4) {
                $start=max(1, $end - 4);
                }
                @endphp

                <div class="flex items-center gap-2">
                <a href="{{ $associates->previousPageUrl() }}" class="px-2 py-1 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 text-xs {{ $current<=1 ? 'opacity-50 pointer-events-none' : '' }}">←</a>

                @if($start > 1)
                <a href="{{ $associates->url(1) }}" class="px-2 py-1 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 text-xs">1</a>
                @if($start > 2)
                <span class="px-1 text-zinc-500 text-sm">…</span>
                @endif
                @endif

                @for($p = $start; $p <= $end; $p++)
                    @if($p==$current)
                    <span class="px-3 py-1 rounded-lg bg-blue-600 text-white font-bold text-xs">{{ $p }}</span>
                    @else
                    <a href="{{ $associates->url($p) }}" class="px-2 py-1 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 text-xs">{{ $p }}</a>
                    @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                        <span class="px-1 text-zinc-500 text-sm">…</span>
                        @endif
                        <a href="{{ $associates->url($last) }}" class="px-2 py-1 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 text-xs">{{ $last }}</a>
                        @endif

                        <a href="{{ $associates->nextPageUrl() }}" class="px-2 py-1 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 text-xs {{ $current>=$last ? 'opacity-50 pointer-events-none' : '' }}">→</a>

                        <div class="text-zinc-600 text-xs ml-3">({{ $associates->total() }} total)</div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-zinc-800/50 text-zinc-500 text-[10px] font-black uppercase tracking-widest border-b border-zinc-800">
                <tr>
                    <th class="px-6 py-4 cursor-pointer hover:text-blue-400" wire:click="sortBy('status')">
                        Estado @if($sortBy==='status')<span class="text-blue-400">{{ $sortDirection==='asc'?'↑':'↓' }}</span>@endif
                    </th>
                    <th class="px-6 py-4 cursor-pointer hover:text-blue-400" wire:click="sortBy('sector_id')">
                        Sector @if($sortBy==='sector_id')<span class="text-blue-400">{{ $sortDirection==='asc'?'↑':'↓' }}</span>@endif
                    </th>
                    <th class="px-6 py-4 cursor-pointer hover:text-blue-400" wire:click="sortBy('last_name')">
                        Apellidos y Nombres @if($sortBy==='last_name')<span class="text-blue-400">{{ $sortDirection==='asc'?'↑':'↓' }}</span>@endif
                    </th>
                    <th class="px-6 py-4 cursor-pointer hover:text-blue-400" wire:click="sortBy('dni')">
                        DNI @if($sortBy==='dni')<span class="text-blue-400">{{ $sortDirection==='asc'?'↑':'↓' }}</span>@endif
                    </th>
                    <th class="px-6 py-4">Dirección / Referencia</th>
                    <th class="px-6 py-4 cursor-pointer hover:text-blue-400" wire:click="sortBy('entry_date')">
                        Ingreso @if($sortBy==='entry_date')<span class="text-blue-400">{{ $sortDirection==='asc'?'↑':'↓' }}</span>@endif
                    </th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/50">

                @forelse($associates as $socio)

                {{-- ── FILA SOCIO PRINCIPAL ── --}}
                <tr wire:key="socio-{{ $socio->id }}" class="hover:bg-zinc-800/20 transition-all {{ $socio->status === 'suspendido' ? 'opacity-60' : '' }}">

                    {{-- Estado --}}
                    <td class="px-6 py-4">
                        @if($socio->status === 'activo')
                        <span class="bg-green-500/10 border border-green-500/30 text-green-400 text-[10px] font-black px-2.5 py-1 rounded-full uppercase">Activo</span>
                        @else
                        <span class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-[10px] font-black px-2.5 py-1 rounded-full uppercase">Suspendido</span>
                        @endif
                    </td>

                    {{-- Sector --}}
                    <td class="px-6 py-4">
                        <span class="bg-zinc-800 text-zinc-300 text-[10px] font-black px-2.5 py-1 rounded-lg border border-zinc-700">
                            {{ $socio->sector->name ?? 'Sin sector' }}
                        </span>
                    </td>

                    {{-- Nombre --}}
                    <td class="px-6 py-4">
                        <div class="text-white font-bold text-sm uppercase">{{ $socio->last_name }}, {{ $socio->name }}</div>
                    </td>

                    {{-- DNI --}}
                    <td class="px-6 py-4 text-zinc-400 font-mono text-sm">{{ $socio->dni ?? '—' }}</td>

                    {{-- Dirección --}}
                    <td class="px-6 py-4">
                        <div class="text-zinc-400 text-xs">{{ $socio->address ?? '—' }}</div>
                        @if($socio->address_reference)
                        <div class="text-zinc-600 text-[10px] italic">{{ $socio->address_reference }}</div>
                        @endif
                    </td>

                    {{-- Fecha ingreso --}}
                    <td class="px-6 py-4 text-zinc-500 text-xs">{{ $socio->entry_date->format('d/m/Y') }}</td>

                    {{-- Acciones --}}
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-1.5 flex-wrap">

                            {{-- Editar --}}
                            <button wire:click="editSocio({{ $socio->id }})"
                                class="p-2.5 bg-blue-600/20 text-blue-400 rounded-lg hover:bg-blue-600 hover:text-white transition-all"
                                title="Editar socio">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            {{-- Agregar instalación --}}
                            <button wire:click="abrirNuevaConexion({{ $socio->id }})"
                                class="p-2.5 bg-cyan-600/20 text-cyan-400 rounded-lg hover:bg-cyan-600 hover:text-white transition-all"
                                title="Agregar instalación adicional">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </button>

                            {{-- Suspender / Activar --}}
                            <button wire:click="toggleStatus({{ $socio->id }})"
                                class="p-2.5 rounded-lg transition-all {{ $socio->status === 'activo' ? 'bg-yellow-600/20 text-yellow-400 hover:bg-yellow-600 hover:text-white' : 'bg-green-600/20 text-green-400 hover:bg-green-600 hover:text-white' }}"
                                title="{{ $socio->status === 'activo' ? 'Suspender' : 'Reactivar' }}">
                                @if($socio->status === 'activo')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @endif
                            </button>

                            {{-- Eliminar --}}
                            @if($confirmingDelete === $socio->id)
                            <button wire:key="confirm-delete-{{ $socio->id }}" wire:click="eliminarSocio({{ $socio->id }})"
                                class="bg-red-600 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-red-700 transition-colors">
                                Confirmar
                            </button>
                            <button wire:click="cancelDelete"
                                class="bg-zinc-700 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-zinc-600 transition-colors">
                                Cancelar
                            </button>
                            @else
                            <button wire:click="confirmDelete({{ $socio->id }})"
                                class="p-2.5 bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all"
                                title="Eliminar socio">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif

                        </div>
                    </td>
                </tr>

                {{-- ── FILAS DE INSTALACIONES ADICIONALES ── --}}
                @if(isset($conexionesAdicionales[$socio->id]))
                @foreach($conexionesAdicionales[$socio->id] as $conn)
                <tr wire:key="connection-{{ $conn->id }}" class="border-l-4 border-cyan-500/50 bg-cyan-950/10 hover:bg-cyan-950/20 transition-all">

                    {{-- Estado de la instalación --}}
                    <td class="px-6 py-3">
                        @if($conn->status === 'activo')
                        <span class="bg-green-500/10 border border-green-500/30 text-green-400 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Activo</span>
                        @else
                        <span class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Suspendido</span>
                        @endif
                    </td>

                    {{-- Sector de la instalación --}}
                    <td class="px-6 py-3">
                        <span class="bg-cyan-900/30 text-cyan-300 text-[10px] font-black px-2.5 py-1 rounded-lg border border-cyan-700/40">
                            {{ $conn->sector?->name ?? 'Sin sector' }}
                        </span>
                    </td>

                    {{-- Nombre + badge instalación --}}
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400 font-bold text-sm uppercase">{{ $socio->last_name }}, {{ $socio->name }}</span>
                            <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 uppercase whitespace-nowrap">
                                {{ $conn->label }}
                            </span>
                        </div>
                    </td>

                    {{-- DNI (mismo del socio) --}}
                    <td class="px-6 py-3 text-zinc-600 font-mono text-sm">{{ $socio->dni ?? '—' }}</td>

                    {{-- Dirección + medidor de esta instalación --}}
                    <td class="px-6 py-3">
                        <div class="text-zinc-500 text-xs">{{ $conn->address ?? '—' }}</div>
                        @if($conn->meter_number)
                        <div class="text-zinc-600 text-[10px]">🔢 {{ $conn->meter_number }}</div>
                        @endif
                    </td>

                    {{-- Fecha inicio de cobro --}}
                    <td class="px-6 py-3 text-cyan-600 text-xs font-bold">
                        {{ $conn->entry_date?->format('d/m/Y') ?? '—' }}
                        <div class="text-zinc-700 text-[9px] font-normal">Inicio cobro</div>
                    </td>

                    {{-- Acciones instalación --}}
                    <td class="px-6 py-3">
                        <div class="flex justify-center items-center gap-1.5">
                            <button wire:click="editarConexion({{ $conn->id }})"
                                class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:bg-blue-600 hover:text-white transition-all"
                                title="Editar instalación">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="eliminarConexion({{ $conn->id }})"
                                class="p-2 bg-zinc-800 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all"
                                title="Eliminar instalación"
                                onclick="return confirm('¿Eliminar esta instalación? Solo si no tiene pagos.')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif

                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-zinc-700 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-zinc-600 italic mb-2">No se encontraron socios con esos criterios</p>
                            @if($search || $filterStatus || $filterSector || $filterDateFrom || $filterDateTo)
                            <button wire:click="resetFilters" class="text-blue-400 hover:text-blue-300 font-bold text-xs">
                                Limpiar filtros
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Paginación compacta --}}
    @if($associates->hasPages())
    <div class="p-6 border-t border-zinc-800 bg-zinc-800/20">
        @php
        $current = $associates->currentPage();
        $last = $associates->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        if ($end - $start < 4) {
            $start=max(1, $end - 4);
            }
            @endphp

            <div class="flex items-center justify-center gap-2">
            {{-- Prev --}}
            <a href="{{ $associates->previousPageUrl() }}" @if($current<=1) aria-disabled="true" @endif
                class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm {{ $current<=1 ? 'opacity-50 pointer-events-none' : '' }}">←</a>

            {{-- First + leading ellipsis --}}
            @if($start > 1)
            <a href="{{ $associates->url(1) }}" class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm">1</a>
            @if($start > 2)
            <span class="px-2 text-zinc-500">…</span>
            @endif
            @endif

            {{-- Page numbers --}}
            @for($p = $start; $p <= $end; $p++)
                @if($p==$current)
                <span class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-sm">{{ $p }}</span>
                @else
                <a href="{{ $associates->url($p) }}" class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm">{{ $p }}</a>
                @endif
                @endfor

                {{-- Trailing ellipsis + Last --}}
                @if($end < $last)
                    @if($end < $last - 1)
                    <span class="px-2 text-zinc-500">…</span>
                    @endif
                    <a href="{{ $associates->url($last) }}" class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm">{{ $last }}</a>
                    @endif

                    {{-- Next --}}
                    <a href="{{ $associates->nextPageUrl() }}" @if($current>=$last) aria-disabled="true" @endif
                        class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm {{ $current>=$last ? 'opacity-50 pointer-events-none' : '' }}">→</a>
    </div>
</div>
@endif
</div>

{{-- ══════════════════════════════════════════════════════════════════
         MODAL NUEVA / EDITAR INSTALACIÓN ADICIONAL
         ══════════════════════════════════════════════════════════════════ --}}
@if($showConnectionModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-zinc-700 rounded-[2rem] shadow-2xl w-full max-w-md mx-4">

        <div class="px-6 py-5 border-b border-zinc-800 flex items-center justify-between">
            <div>
                <h3 class="text-white font-black uppercase italic text-sm">
                    {{ $connection_id ? 'Editar Instalación' : 'Nueva Instalación' }}
                </h3>
                <p class="text-zinc-500 text-[10px] mt-0.5">Aparecerá en la tabla y en cobros de forma independiente</p>
            </div>
            <button wire:click="$set('showConnectionModal', false)" class="text-zinc-500 hover:text-white transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4">

            {{-- Nombre --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Nombre de la Instalación *</label>
                <input wire:model="conn_label" type="text" placeholder="Ej: Casa 2, Casa Alta, Tienda..."
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                @error('conn_label') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Sector --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Sector de esta Instalación *</label>
                <select wire:model="conn_sector_id"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                    <option value="">-- Seleccionar sector --</option>
                    @foreach($sectores as $sector)
                    <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                    @endforeach
                </select>
                @error('conn_sector_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Fecha inicio cobro --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha de Inicio de Cobro *</label>
                <input wire:model="conn_entry_date" type="date"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                <p class="text-zinc-600 text-[10px] mt-1">Desde este mes se calcularán las cuotas pendientes.</p>
                @error('conn_entry_date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Dirección --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Dirección</label>
                <input wire:model="conn_address" type="text" placeholder="Ej: Jr. Los Álamos 456"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
            </div>

            {{-- Medidor --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">N° de Medidor</label>
                <input wire:model="conn_meter_number" type="text" placeholder="Ej: MED-00456"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-cyan-500">
            </div>

            {{-- Estado --}}
            <div>
                <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Estado</label>
                <select wire:model="conn_status"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                    <option value="activo">Activo</option>
                    <option value="suspendido">Suspendido</option>
                </select>
            </div>

        </div>

        <div class="px-6 pb-6 flex gap-3">
            <button wire:click="$set('showConnectionModal', false)"
                class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-black text-xs uppercase py-3 rounded-xl transition-colors">
                Cancelar
            </button>
            <button wire:click="guardarConexion"
                class="flex-1 bg-cyan-600 hover:bg-cyan-500 text-white font-black text-xs uppercase py-3 rounded-xl transition-colors">
                {{ $connection_id ? 'Actualizar' : 'Registrar Instalación' }}
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
         MODAL CREAR / EDITAR SOCIO
         ══════════════════════════════════════════════════════════════════ --}}
@if($showModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" wire:click="$set('showModal', false)"></div>

    <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="p-8">

            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-blue-600/20 text-blue-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-black uppercase italic text-xl">
                        {{ $isEditMode ? 'Editar Socio' : 'Nuevo Socio' }}
                    </h3>
                    <p class="text-zinc-500 text-xs">
                        {{ $isEditMode ? 'Modifica los datos del socio' : 'Registra un nuevo socio al padrón' }}
                    </p>
                </div>
            </div>

            <div class="space-y-5">

                {{-- DNI + RENIEC --}}
                <div>
                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                        DNI *
                        @if(!$isEditMode)
                        <span class="text-zinc-600 font-normal normal-case ml-1">— se consultará RENIEC automáticamente</span>
                        @endif
                    </label>
                    <div class="relative">
                        <input wire:model.live="dni" type="text" inputmode="numeric" maxlength="8"
                            placeholder="Ingresa los 8 dígitos"
                            class="w-full bg-zinc-800 border text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none pr-10
                                    @if($reniecEstado === 'ok') border-green-600 focus:border-green-500
                                    @elseif($reniecEstado === 'error') border-amber-600 focus:border-amber-500
                                    @else border-zinc-700 focus:border-blue-500 @endif">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            @if($reniecEstado === 'cargando')
                            <svg class="animate-spin w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                            </svg>
                            @elseif($reniecEstado === 'ok')
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            @elseif($reniecEstado === 'error')
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            @endif
                        </div>
                    </div>
                    @if($reniecEstado === 'cargando')
                    <p class="text-blue-400 text-[10px] font-bold mt-1.5 flex items-center gap-1">
                        <span class="animate-pulse">●</span> Consultando RENIEC...
                    </p>
                    @elseif($reniecEstado === 'ok')
                    <p class="text-green-400 text-[10px] font-bold mt-1.5">✓ Datos cargados desde RENIEC — puedes editar si es necesario</p>
                    @elseif($reniecEstado === 'error')
                    <p class="text-amber-400 text-[10px] font-bold mt-1.5">⚠ {{ $reniecMensaje }}</p>
                    @endif
                    @error('dni') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Nombres y Apellidos --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                            Nombres *
                            @if($reniecEstado === 'ok')<span class="text-green-500/70 font-normal normal-case">· RENIEC</span>@endif
                        </label>
                        <input wire:model.live="name" type="text" placeholder="Se llena automáticamente"
                            class="w-full bg-zinc-800 border text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none
                                    @if($reniecEstado === 'ok') border-green-600/50 focus:border-green-500 @else border-zinc-700 focus:border-blue-500 @endif">
                        @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                            Apellidos *
                            @if($reniecEstado === 'ok')<span class="text-green-500/70 font-normal normal-case">· RENIEC</span>@endif
                        </label>
                        <input wire:model.live="last_name" type="text" placeholder="Se llena automáticamente"
                            class="w-full bg-zinc-800 border text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none
                                    @if($reniecEstado === 'ok') border-green-600/50 focus:border-green-500 @else border-zinc-700 focus:border-blue-500 @endif">
                        @error('last_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Sector, Fecha, Estado --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Sector / Zona *</label>
                        <select wire:model="sector_id"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">-- Seleccionar --</option>
                            @foreach($sectores as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                            @endforeach
                        </select>
                        @error('sector_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha de Inscripción *</label>
                        <input wire:model="entry_date" type="date"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('entry_date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Estado</label>
                        <select wire:model="status"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="activo">Activo</option>
                            <option value="suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>

                {{-- Medidor --}}
                <div>
                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">N° de Medidor</label>
                    <input wire:model="meter_number" type="text" placeholder="Ej: MED-00123"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-blue-500">
                    @error('meter_number') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Dirección --}}
                <div>
                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                        Dirección
                        @if($reniecEstado === 'ok' && $address)
                        <span class="text-green-500/70 font-normal normal-case">· desde RENIEC (editable)</span>
                        @endif
                    </label>
                    <input wire:model="address" type="text" placeholder="Ej: Jr. Los Pinos 123"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                    @error('address') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Referencia --}}
                <div>
                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Manzana / Lote / Referencia</label>
                    <input wire:model="address_reference" type="text" placeholder="Ej: Mz. B Lt. 5, frente a la iglesia"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                    @error('address_reference') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

            </div>

            {{-- Botones --}}
            <div class="mt-8 flex gap-4">
                <button wire:click="saveSocio" wire:loading.attr="disabled"
                    class="flex-1 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3.5 rounded-2xl uppercase text-sm transition-colors">
                    <span wire:loading.remove wire:target="saveSocio">{{ $isEditMode ? 'Confirmar Cambios' : 'Registrar Socio' }}</span>
                    <span wire:loading wire:target="saveSocio">Guardando...</span>
                </button>
                <button wire:click="$set('showModal', false)"
                    class="px-8 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-colors uppercase text-sm">
                    Cancelar
                </button>
            </div>

        </div>
    </div>
</div>
@endif

</div>
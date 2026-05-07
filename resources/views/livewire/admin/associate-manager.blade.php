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

    {{-- ====================================================================== --}}
    {{-- ENCABEZADO + ESTADÍSTICAS --}}
    {{-- ====================================================================== --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
        <div>
            <h2 class="text-white font-black uppercase italic text-2xl">Padrón General de Socios</h2>
            <p class="text-zinc-500 text-xs font-bold mt-1">
                Total: <span class="text-blue-400">{{ $totalActivos + $totalSuspendidos }}</span> socios registrados
            </p>
        </div>

        <div class="flex gap-3 flex-wrap">
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center hover:border-green-600/50 transition-colors">
                <div class="text-green-400 text-xl font-black">{{ $totalActivos }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">Activos</div>
            </div>
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center hover:border-yellow-600/50 transition-colors">
                <div class="text-yellow-400 text-xl font-black">{{ $totalSuspendidos }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">Suspendidos</div>
            </div>
            <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl px-5 py-3 text-center hover:border-blue-600/50 transition-colors">
                <div class="text-blue-400 text-xl font-black">{{ $associates->count() }}</div>
                <div class="text-zinc-500 text-[10px] font-bold uppercase">En página</div>
            </div>
            <button wire:click="openModal"
                class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl uppercase text-xs flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Socio
            </button>
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- FILTROS Y BUSQUEDA --}}
    {{-- ====================================================================== --}}
    <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-4 mb-6">
        {{-- Fila 1: Búsqueda Principal --}}
        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Buscar por nombre, apellido, DNI o N° de medidor..."
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
        </div>

        {{-- Fila 2: Filtros adicionales --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            {{-- Filtro por estado --}}
            <div>
                <select wire:model.live="filterStatus"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
                    <option value="">Todos estados</option>
                    <option value="activo">Activos</option>
                    <option value="suspendido">Suspendidos</option>
                </select>
            </div>

            {{-- Filtro por sector --}}
            <div>
                <select wire:model.live="filterSector"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
                    <option value="">Todos sectores</option>
                    @foreach($sectores as $sector)
                    <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro fecha desde --}}
            <div>
                <label class="text-zinc-600 text-[10px] font-bold uppercase block mb-1">Desde</label>
                <input wire:model.live="filterDateFrom" type="date"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
            </div>

            {{-- Filtro fecha hasta --}}
            <div>
                <label class="text-zinc-600 text-[10px] font-bold uppercase block mb-1">Hasta</label>
                <input wire:model.live="filterDateTo" type="date"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
            </div>

            {{-- Bot\u00f3n limpiar filtros --}}
            @if($search || $filterStatus || $filterSector || $filterDateFrom || $filterDateTo)
            <button wire:click="resetFilters"
                class="bg-red-600/20 hover:bg-red-600/40 border border-red-600/50 text-red-400 rounded-xl px-3 py-2 text-xs font-bold transition-colors">
                <!-- \u2717  -->Limpiar
            </button>
            @endif
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- TABLA --}}
    {{-- ====================================================================== --}}
    <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
        {{-- Control de paginación y filas por página --}}
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
            <div class="text-zinc-500 font-bold">
                Página {{ $associates->currentPage() }} de {{ $associates->lastPage() }}
                <span class="text-zinc-600">({{ $associates->total() }} total)</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-800/50 text-zinc-500 text-[10px] font-black uppercase tracking-widest border-b border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-400 transition-colors" wire:click="sortBy('status')">
                            <span class="flex items-center gap-1">
                                Estado
                                @if($sortBy === 'status')
                                    <span class="text-blue-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-400 transition-colors" wire:click="sortBy('sector_id')">
                            <span class="flex items-center gap-1">
                                Sector
                                @if($sortBy === 'sector_id')
                                    <span class="text-blue-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-400 transition-colors" wire:click="sortBy('last_name')">
                            <span class="flex items-center gap-1">
                                Apellidos y Nombres
                                @if($sortBy === 'last_name')
                                    <span class="text-blue-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-400 transition-colors" wire:click="sortBy('dni')">
                            <span class="flex items-center gap-1">
                                DNI
                                @if($sortBy === 'dni')
                                    <span class="text-blue-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4">Dirección / Referencia</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-400 transition-colors" wire:click="sortBy('entry_date')">
                            <span class="flex items-center gap-1">
                                Ingreso
                                @if($sortBy === 'entry_date')
                                    <span class="text-blue-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse($associates as $socio)
                    <tr class="hover:bg-zinc-800/20 transition-all {{ $socio->status === 'suspendido' ? 'opacity-60' : '' }}">

                        {{-- Estado --}}
                        <td class="px-6 py-4">
                            @if($socio->status === 'activo')
                            <span class="bg-green-500/10 border border-green-500/30 text-green-400 text-[10px] font-black px-2.5 py-1 rounded-full uppercase">
                                Activo
                            </span>
                            @else
                            <span class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-[10px] font-black px-2.5 py-1 rounded-full uppercase">
                                Suspendido
                            </span>
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
                        <td class="px-6 py-4 text-zinc-400 font-mono text-sm">{{ $socio->dni }}</td>

                        {{-- Dirección --}}
                        <td class="px-6 py-4">
                            <div class="text-zinc-400 text-xs">{{ $socio->address ?? '—' }}</div>
                            @if($socio->address_reference)
                            <div class="text-zinc-600 text-[10px] italic">{{ $socio->address_reference }}</div>
                            @endif
                        </td>

                        {{-- Fecha ingreso --}}
                        <td class="px-6 py-4 text-zinc-500 text-xs">
                            {{ $socio->entry_date->format('d/m/Y') }}
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center gap-1.5">

                                {{-- Editar --}}
                                <button wire:click="editSocio({{ $socio->id }})"
                                    class="p-2.5 bg-blue-600/20 text-blue-400 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200 flex items-center justify-center"
                                    title="Editar socio">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Suspender / Activar --}}
                                <button wire:click="toggleStatus({{ $socio->id }})"
                                    class="p-2.5 rounded-lg transition-all duration-200 flex items-center justify-center {{ $socio->status === 'activo' ? 'bg-yellow-600/20 text-yellow-400 hover:bg-yellow-600 hover:text-white' : 'bg-green-600/20 text-green-400 hover:bg-green-600 hover:text-white' }}"
                                    title="{{ $socio->status === 'activo' ? 'Suspender socio' : 'Reactivar socio' }}">
                                    @if($socio->status === 'activo')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </button>

                                {{-- Eliminar con confirmación --}}
                                @if($confirmingDelete === $socio->id)
                                <button wire:click="eliminarSocio({{ $socio->id }})"
                                    class="bg-red-600 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-red-700 transition-colors">
                                    Confirmar
                                </button>
                                <button wire:click="cancelDelete"
                                    class="bg-zinc-700 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-zinc-600 transition-colors">
                                    Cancelar
                                </button>
                                @else
                                <button wire:click="confirmDelete({{ $socio->id }})"
                                    class="p-2.5 bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 flex items-center justify-center"
                                    title="Eliminar socio">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto text-zinc-700 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-zinc-600 italic mb-2">No se encontraron socios con esos criterios</p>
                                <p class="text-zinc-700 text-xs">
                                    @if($search || $filterStatus || $filterSector || $filterDateFrom || $filterDateTo)
                                        Intenta ajustar los filtros o 
                                        <button wire:click="resetFilters" class="text-blue-400 hover:text-blue-300 font-bold">
                                            limpiar los filtros
                                        </button>
                                    @else
                                        Comienza registrando tu primer socio
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($associates->hasPages())
        <div class="p-6 border-t border-zinc-800 bg-zinc-800/20">
            <div class="flex items-center justify-center gap-1">
                {{-- Anterior --}}
                <a href="{{ $associates->previousPageUrl() }}" 
                   class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm"
                   @disabled($associates->onFirstPage())>
                    ← Anterior
                </a>

                {{-- Números de página --}}
                @foreach($associates->getUrlRange(1, $associates->lastPage()) as $page => $url)
                    @if($page == $associates->currentPage())
                    <span class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-sm">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach

                {{-- Siguiente --}}
                <a href="{{ $associates->nextPageUrl() }}" 
                   class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:border-blue-500 hover:text-blue-400 transition-colors text-sm"
                   @disabled(!$associates->hasMorePages())>
                    Siguiente →
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ====================================================================== --}}
    {{-- MODAL CREAR / EDITAR --}}
    {{-- ====================================================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-md" wire:click="$set('showModal', false)"></div>

        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <div class="p-8">

                {{-- Título --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-blue-600/20 text-blue-400 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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

                    {{-- Sector y Estado --}}
                    <div class="grid grid-cols-2 gap-4">
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
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Estado</label>
                            <select wire:model="status"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                                <option value="activo">Activo</option>
                                <option value="suspendido">Suspendido</option>
                            </select>
                            @error('status') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Nombre y Apellido --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Nombres *</label>
                            <input wire:model="name" type="text" placeholder="Ej: Juan Carlos"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Apellidos *</label>
                            <input wire:model="last_name" type="text" placeholder="Ej: García Pérez"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('last_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- DNI y Fecha --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">DNI *</label>
                            <input wire:model="dni" type="text" maxlength="8" placeholder="12345678"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-blue-500">
                            @error('dni') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha de Inscripción *</label>
                            <input wire:model="entry_date" type="date"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('entry_date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- N° Medidor --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">N° de Medidor</label>
                        <input wire:model="meter_number" type="text" placeholder="Ej: MED-00123"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-blue-500">
                        @error('meter_number') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Dirección --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Dirección</label>
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
                        <span wire:loading.remove wire:target="saveSocio">
                            {{ $isEditMode ? 'Confirmar Cambios' : 'Registrar Socio' }}
                        </span>
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
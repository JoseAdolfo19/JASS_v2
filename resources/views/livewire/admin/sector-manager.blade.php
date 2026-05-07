<div class="max-w-6xl mx-auto py-8 px-4">

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
    {{-- ENCABEZADO + ACCIONES --}}
    {{-- ====================================================================== --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
        <div>
            <h2 class="text-white font-black uppercase italic text-2xl">Gestión de Sectores</h2>
            <p class="text-zinc-500 text-xs font-bold mt-1">
                Total: <span class="text-blue-400">{{ count($sectors) }}</span> sectores registrados
            </p>
        </div>

        <button wire:click="openForm"
            class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl uppercase text-xs flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Sector
        </button>
    </div>

    {{-- ====================================================================== --}}
    {{-- BÚSQUEDA --}}
    {{-- ====================================================================== --}}
    @if(count($sectors) > 0)
    <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-4 mb-6">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Buscar sector..."
            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
    </div>
    @endif

    {{-- ====================================================================== --}}
    {{-- LISTADO DE SECTORES --}}
    {{-- ====================================================================== --}}
    <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
        @if(count($sectors) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-800/50 text-zinc-500 text-[10px] font-black uppercase tracking-widest border-b border-zinc-800">
                    <tr>
                        <th class="px-6 py-4">Sector</th>
                        <th class="px-6 py-4 text-center">Socios</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @foreach($sectors as $sector)
                    <tr class="hover:bg-zinc-800/20 transition-all">
                        <td class="px-6 py-4">
                            <div class="text-white font-bold text-sm">{{ $sector->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center bg-blue-600/20 text-blue-400 text-xs font-black px-3 py-1.5 rounded-lg border border-blue-600/50">
                                {{ $sector->associates_count ?? $sector->associates()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Editar --}}
                                <button wire:click="editSector({{ $sector->id }})"
                                    class="p-2.5 bg-blue-600/20 text-blue-400 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200 flex items-center justify-center"
                                    title="Editar sector">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Eliminar con confirmación --}}
                                @if($confirmingId === $sector->id)
                                <button wire:click="delete({{ $sector->id }})"
                                    class="bg-red-600 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-red-700 transition-colors">
                                    Confirmar
                                </button>
                                <button wire:click="cancelDelete"
                                    class="bg-zinc-700 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg hover:bg-zinc-600 transition-colors">
                                    Cancelar
                                </button>
                                @else
                                <button wire:click="confirmDelete({{ $sector->id }})"
                                    class="p-2.5 bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 flex items-center justify-center"
                                    title="Eliminar sector">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto text-zinc-700 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-zinc-600 italic mb-3">No hay sectores registrados</p>
                <button wire:click="openForm"
                    class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear primer sector
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- ====================================================================== --}}
    {{-- MODAL CREAR / EDITAR --}}
    {{-- ====================================================================== --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-md" wire:click="$set('showForm', false)"></div>

        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden">
            <div class="p-8">

                {{-- Título --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-blue-600/20 text-blue-400 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-black uppercase italic text-xl">
                            {{ $editingId ? 'Editar Sector' : 'Nuevo Sector' }}
                        </h3>
                        <p class="text-zinc-500 text-xs">
                            {{ $editingId ? 'Modifica el nombre del sector' : 'Crea un nuevo sector' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Nombre --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-2">Nombre del Sector *</label>
                        <input wire:model="name" type="text"
                            placeholder="Ej: Sector Alto, Zona Centro..."
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Botones --}}
                <div class="mt-8 flex gap-4">
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3.5 rounded-2xl uppercase text-sm transition-colors">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Guardar Cambios' : 'Crear Sector' }}
                        </span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>
                    <button wire:click="$set('showForm', false)"
                        class="px-8 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-colors uppercase text-sm">
                        Cancelar
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>
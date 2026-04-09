<div class="max-w-7xl mx-auto py-8 px-4">

    {{-- FLASH MESSAGE --}}
    @if (session()->has('message'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-2xl text-sm font-bold">
        ✅ {{ session('message') }}
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ================================================================ --}}
        {{-- FORMULARIO DE NUEVO GASTO --}}
        {{-- ================================================================ --}}
        <div class="w-full lg:w-1/3">
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl sticky top-8">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic text-lg">Registrar Gasto</h3>
                    <p class="text-zinc-500 text-[10px] font-bold">Nuevo egreso con comprobante</p>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Descripción --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Descripción *</label>
                        <input wire:model="description" type="text" placeholder="Ej: Compra de tubería PVC"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Categoría *</label>
                        <select wire:model="category"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="Materiales">Materiales</option>
                            <option value="Servicios">Servicios</option>
                            <option value="Planilla">Planilla</option>
                            <option value="Viáticos">Viáticos</option>
                            <option value="Otros">Otros</option>
                        </select>
                        @error('category') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Monto y Fecha --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Monto *</label>
                            <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('amount') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha *</label>
                            <input wire:model="date" type="date"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Número de Voucher --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">N° Boleta / Factura</label>
                        <input wire:model="voucher_number" type="text" placeholder="Ej: B001-00123"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('voucher_number') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Subida de Imagen --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Foto del Comprobante</label>

                        {{-- Preview de imagen antes de guardar --}}
                        @if ($voucher)
                        <div class="mb-3 relative">
                            <img src="{{ $voucher->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl border border-zinc-700">
                            <button wire:click="$set('voucher', null)"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center font-black">
                                ✕
                            </button>
                        </div>
                        @endif

                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-zinc-600 rounded-xl cursor-pointer hover:border-blue-500 transition-colors">
                            <svg class="w-6 h-6 text-zinc-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-zinc-500 text-xs">JPG, PNG — máx. 4MB</span>
                            <input wire:model="voucher" type="file" accept="image/*" class="hidden">
                        </label>

                        {{-- Loading mientras sube --}}
                        <div wire:loading wire:target="voucher" class="text-blue-400 text-xs mt-1 text-center">
                            Subiendo imagen...
                        </div>

                        @error('voucher') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botón Guardar --}}
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3 rounded-xl uppercase text-sm transition-colors">
                        <span wire:loading.remove wire:target="save">💾 Registrar Gasto</span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>

                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- TABLA DE EGRESOS --}}
        {{-- ================================================================ --}}
        <div class="w-full lg:w-2/3 space-y-6">

            {{-- Resumen --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-5 text-center">
                    <div class="text-red-400 text-2xl font-black">S/ {{ number_format($totalMes, 2) }}</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase mt-1">Egresos este mes</div>
                </div>
                <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-5 text-center">
                    <div class="text-orange-400 text-2xl font-black">S/ {{ number_format($totalGeneral, 2) }}</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase mt-1">Total general</div>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-4">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por descripción..."
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
            </div>

            {{-- Lista de gastos --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic text-lg">Historial de Egresos</h3>
                    <p class="text-zinc-500 text-[10px] font-bold">Registro completo de gastos con comprobantes</p>
                </div>

                <div class="divide-y divide-zinc-800">
                    @forelse($expenses as $expense)
                    <div class="p-4 flex items-start gap-4">

                        {{-- Thumbnail del voucher --}}
                        <div class="flex-shrink-0 w-14 h-14">
                            @if($expense->voucher_path)
                            <a href="{{ Storage::url($expense->voucher_path) }}" target="_blank">
                                <img src="{{ Storage::url($expense->voucher_path) }}"
                                    class="w-14 h-14 object-cover rounded-xl border border-zinc-700 hover:border-blue-500 transition-colors cursor-pointer">
                            </a>
                            @else
                            <div class="w-14 h-14 bg-zinc-800 rounded-xl border border-zinc-700 flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            @endif
                        </div>

                        {{-- Info del gasto --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="text-white font-bold text-sm">{{ $expense->description }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="bg-zinc-700 text-zinc-300 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            {{ $expense->category }}
                                        </span>
                                        @if($expense->voucher_number)
                                        <span class="text-zinc-500 text-[10px]">{{ $expense->voucher_number }}</span>
                                        @endif
                                    </div>
                                    <div class="text-zinc-500 text-xs mt-1">
                                        {{ $expense->date->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-red-400 font-black text-lg">S/ {{ number_format($expense->amount, 2) }}</div>

                                    {{-- Botón eliminar con confirmación --}}
                                    @if($confirmingDelete === $expense->id)
                                    <div class="flex gap-1 mt-1">
                                        <button wire:click="delete({{ $expense->id }})"
                                            class="bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded-lg">
                                            Confirmar
                                        </button>
                                        <button wire:click="cancelDelete"
                                            class="bg-zinc-700 text-white text-[10px] font-black px-2 py-1 rounded-lg">
                                            Cancelar
                                        </button>
                                    </div>
                                    @else
                                    <button wire:click="confirmDelete({{ $expense->id }})"
                                        class="text-zinc-600 hover:text-red-400 text-[10px] font-bold mt-1 transition-colors">
                                        Eliminar
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    @empty
                    <div class="p-12 text-center text-zinc-500">
                        No hay gastos registrados aún.
                    </div>
                    @endforelse
                </div>

                {{-- Paginación --}}
                @if($expenses->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $expenses->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
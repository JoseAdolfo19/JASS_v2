<div class="max-w-7xl mx-auto py-8 px-4">

    @if(session('message'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-2xl text-sm font-bold flex justify-between">
        <span>✅ {{ session('message') }}</span>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ================================================================ --}}
        {{-- FORMULARIO --}}
        {{-- ================================================================ --}}
        <div class="w-full lg:w-1/3">
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl sticky top-8">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic text-lg">Registrar Egreso</h3>
                    <p class="text-zinc-500 text-[10px] font-bold">Nuevo gasto con comprobante</p>
                </div>

                <div class="p-6 space-y-4">

                    {{-- TIPO DE COMPROBANTE --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-2">Tipo de Comprobante *</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['boleta',             '🧾', 'Boleta'],
                                ['factura',            '📄', 'Factura'],
                                ['recibo_honorarios',  '💼', 'Honorarios'],
                                ['declaracion_jurada', '📋', 'Decl. Jurada'],
                            ] as [$val, $icon, $label])
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="voucher_type" value="{{ $val }}" class="sr-only peer">
                                <div class="p-2.5 rounded-xl border-2 border-zinc-700 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 text-center transition-all">
                                    <div class="text-lg">{{ $icon }}</div>
                                    <div class="text-white font-bold text-[10px] uppercase mt-0.5">{{ $label }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('voucher_type') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Descripción *</label>
                        <input wire:model="description" type="text"
                            placeholder="¿En qué se gastó?"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Beneficiario --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                            @if($voucher_type === 'recibo_honorarios') Beneficiario / Trabajador
                            @elseif($voucher_type === 'declaracion_jurada') Declarante
                            @else Proveedor / Empresa
                            @endif
                        </label>
                        <input wire:model="beneficiary" type="text"
                            placeholder="Nombre completo o razón social"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('beneficiary') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- RUC / DNI --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">
                            {{ in_array($voucher_type, ['boleta','factura']) ? 'RUC del Emisor' : 'DNI del Beneficiario' }}
                        </label>
                        <input wire:model="ruc_dni" type="text"
                            placeholder="{{ in_array($voucher_type, ['boleta','factura']) ? '20xxxxxxxxx' : '12345678' }}"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-blue-500">
                        @error('ruc_dni') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Categoría, Monto y Fecha --}}
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
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Monto *</label>
                            <div class="flex items-center gap-1">
                                <span class="text-zinc-400 text-sm font-bold">S/</span>
                                <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            </div>
                            @error('amount') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Fecha *</label>
                            <input wire:model="date" type="date"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                            @error('date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- N° Comprobante --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">N° Comprobante</label>
                        <input wire:model="voucher_number" type="text"
                            placeholder="{{ in_array($voucher_type, ['boleta','factura']) ? 'B001-00123 / F001-00456' : 'Correlativo' }}"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Observaciones</label>
                        <textarea wire:model="notes" rows="2"
                            placeholder="Detalle adicional, horas trabajadas, concepto específico..."
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                    </div>

                    {{-- Imagen del comprobante --}}
                    <div>
                        <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Foto del Comprobante</label>
                        @if($voucher)
                        <div class="mb-3 relative">
                            <img src="{{ $voucher->temporaryUrl() }}" class="w-full h-36 object-cover rounded-xl border border-zinc-700">
                            <button wire:click="$set('voucher', null)"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center font-black">✕</button>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-zinc-600 rounded-xl cursor-pointer hover:border-blue-500 transition-colors">
                            <svg class="w-5 h-5 text-zinc-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-zinc-500 text-xs">JPG, PNG — máx. 4MB</span>
                            <input wire:model="voucher" type="file" accept="image/*" class="hidden">
                        </label>
                        <div wire:loading wire:target="voucher" class="text-blue-400 text-xs mt-1 text-center">Subiendo imagen...</div>
                        @error('voucher') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="save" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3 rounded-xl uppercase text-sm transition-colors">
                        <span wire:loading.remove wire:target="save">💾 Registrar Egreso</span>
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
                <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl p-5 text-center">
                    <div class="text-red-400 text-2xl font-black">S/ {{ number_format($totalMes, 2) }}</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase mt-1">Egresos este mes</div>
                </div>
                <div class="bg-[#1a1a1a] border border-zinc-800 rounded-2xl p-5 text-center">
                    <div class="text-orange-400 text-2xl font-black">S/ {{ number_format($totalGeneral, 2) }}</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase mt-1">Total general</div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="bg-[#1a1a1a] rounded-2xl border border-zinc-800 p-4 flex gap-3 flex-wrap">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por descripción o proveedor..."
                    class="flex-1 bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 min-w-48">

                <select wire:model.live="filterType"
                    class="bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Todos los tipos</option>
                    <option value="boleta">🧾 Boleta</option>
                    <option value="factura">📄 Factura</option>
                    <option value="recibo_honorarios">💼 Honorarios</option>
                    <option value="declaracion_jurada">📋 Decl. Jurada</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            {{-- Lista --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic text-lg">Historial de Egresos</h3>
                </div>

                <div class="divide-y divide-zinc-800">
                    @forelse($expenses as $expense)
                    <div class="p-4 flex items-start gap-4">

                        {{-- Miniatura --}}
                        <div class="flex-shrink-0 w-14 h-14">
                            @if($expense->voucher_path)
                            <a href="{{ Storage::url($expense->voucher_path) }}" target="_blank">
                                <img src="{{ Storage::url($expense->voucher_path) }}"
                                    class="w-14 h-14 object-cover rounded-xl border border-zinc-700 hover:border-blue-500 transition-colors">
                            </a>
                            @else
                            <div class="w-14 h-14 bg-zinc-800 rounded-xl border border-zinc-700 flex items-center justify-center text-2xl">
                                @switch($expense->voucher_type)
                                    @case('boleta')             🧾 @break
                                    @case('factura')            📄 @break
                                    @case('recibo_honorarios')  💼 @break
                                    @case('declaracion_jurada') 📋 @break
                                    @default                    📁
                                @endswitch
                            </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-white font-bold text-sm truncate">{{ $expense->description }}</div>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        {{-- Badge tipo comprobante --}}
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                                            @switch($expense->voucher_type)
                                                @case('boleta')             bg-blue-500/10 border-blue-500/30 text-blue-400 @break
                                                @case('factura')            bg-purple-500/10 border-purple-500/30 text-purple-400 @break
                                                @case('recibo_honorarios')  bg-green-500/10 border-green-500/30 text-green-400 @break
                                                @case('declaracion_jurada') bg-orange-500/10 border-orange-500/30 text-orange-400 @break
                                                @default                    bg-zinc-700 text-zinc-400
                                            @endswitch
                                        ">
                                            {{ $expense->voucher_type_label }}
                                        </span>
                                        <span class="bg-zinc-700 text-zinc-300 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            {{ $expense->category }}
                                        </span>
                                        @if($expense->voucher_number)
                                        <span class="text-zinc-500 text-[10px]">N° {{ $expense->voucher_number }}</span>
                                        @endif
                                    </div>
                                    @if($expense->beneficiary)
                                    <div class="text-zinc-400 text-xs mt-0.5">{{ $expense->beneficiary }}
                                        @if($expense->ruc_dni) <span class="text-zinc-600">· {{ $expense->ruc_dni }}</span> @endif
                                    </div>
                                    @endif
                                    @if($expense->notes)
                                    <div class="text-zinc-600 text-xs italic mt-0.5 truncate">{{ $expense->notes }}</div>
                                    @endif
                                    <div class="text-zinc-600 text-xs mt-1">{{ $expense->date->format('d/m/Y') }}</div>
                                </div>

                                <div class="text-right flex-shrink-0 space-y-1">
                                    <div class="text-red-400 font-black text-lg">S/ {{ number_format($expense->amount, 2) }}</div>

                                    {{-- Generar PDF --}}
                                    <button wire:click="generarPDF({{ $expense->id }})"
                                        class="text-blue-400 hover:text-blue-300 text-[10px] font-bold flex items-center gap-1 ml-auto transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </button>

                                    {{-- Eliminar --}}
                                    @if($confirmingDelete === $expense->id)
                                    <div class="flex gap-1 justify-end">
                                        <button wire:click="delete({{ $expense->id }})"
                                            class="bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded-lg">Sí</button>
                                        <button wire:click="cancelDelete"
                                            class="bg-zinc-700 text-white text-[10px] font-black px-2 py-1 rounded-lg">No</button>
                                    </div>
                                    @else
                                    <button wire:click="confirmDelete({{ $expense->id }})"
                                        class="text-zinc-600 hover:text-red-400 text-[10px] font-bold transition-colors">
                                        Eliminar
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    @empty
                    <div class="p-12 text-center text-zinc-500 italic">No hay egresos registrados.</div>
                    @endforelse
                </div>

                @if($expenses->hasPages())
                <div class="p-4 border-t border-zinc-800">{{ $expenses->links() }}</div>
                @endif
            </div>

        </div>
    </div>

    @script
    <script>
        $wire.on('open-url', (event) => {
            window.open(event.url, '_blank');
        });
    </script>
    @endscript
</div>
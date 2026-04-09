<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-8">
        <div class="p-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-900/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Caja de Cobranza</h2>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] shadow-2xl overflow-hidden p-8">
        <input type="text" wire:model.live="search" placeholder="Buscar socio..." class="w-full bg-zinc-800 border-none rounded-2xl py-4 px-6 text-white mb-6 focus:ring-2 focus:ring-blue-600 font-bold">

        @if(!empty($associates))
            <div class="mb-6 space-y-2">
                @foreach($associates as $socio)
                    <button wire:click="seleccionarSocio({{ $socio->id }})" class="w-full text-left p-4 bg-zinc-800/50 hover:bg-blue-600/20 rounded-xl border border-zinc-700 transition-all uppercase font-black text-white text-xs">
                        {{ $socio->last_name }}, {{ $socio->name }}
                    </button>
                @endforeach
            </div>
        @endif

        @if($resumenDeuda)
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Monto a cobrar</label>
                    <input type="number" step="0.01" min="0" wire:model.live="montoCobrar" placeholder="S/ 10.00" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl py-3 px-4 text-white font-bold" />
                    <p class="text-zinc-500 text-[10px]">Desde 2026 se cobra S/ 10 por mes; antes puede ser otro valor.</p>
                </div>
                <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">Meses Pendientes</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 max-h-60 overflow-y-auto pr-2">
                    @foreach($resumenDeuda['items'] as $item)
                        <label class="flex items-center gap-2 p-2 bg-zinc-800 rounded-2xl cursor-pointer text-sm">
                            <input type="checkbox" wire:click="toggleMes('{{ $item['meses'][0] }}')" 
                                {{ in_array($item['meses'][0], $mesesSeleccionados) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-blue-600">
                            <span class="text-white font-bold text-[11px]">{{ $item['etiqueta'] }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="bg-black/40 p-6 rounded-3xl border border-zinc-800">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-zinc-500 font-bold text-[10px] uppercase">Mora Calculada</span>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 text-white font-bold cursor-pointer">
                                <input type="checkbox" wire:model.live="aplicarMora" class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-blue-600">
                                Aplicar mora
                            </label>
                            <span class="text-zinc-400">S/ {{ number_format($resumenDeuda['mora_calculada'], 2) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-end pt-4 border-t border-zinc-800">
                        <div>
                            <p class="text-zinc-500 font-bold text-[10px] uppercase">Total Neto</p>
                            <p class="text-5xl text-green-500 font-black italic">S/ {{ number_format($totalFinal, 2) }}</p>
                        </div>
                        <button wire:click="confirmarPago" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-8 py-4 rounded-2xl uppercase text-xs">Cobrar</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
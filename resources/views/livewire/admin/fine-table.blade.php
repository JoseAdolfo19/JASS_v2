<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-8">
        <div class="p-2 bg-amber-600 rounded-xl shadow-lg shadow-amber-900/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Cobro de Multas y Faltas</h2>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] shadow-2xl overflow-hidden p-8">
        <input type="text" wire:model.live="search" placeholder="Buscar socio..."
            class="w-full bg-zinc-800 border-none rounded-2xl py-4 px-6 text-white mb-6 focus:ring-2 focus:ring-amber-600 font-bold">

        @if(empty($multasDetalle))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-br from-amber-900/20 to-amber-900/5 border border-amber-500/20 rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-amber-400 font-black uppercase text-xs tracking-widest">Cómo usar</h4>
                    </div>
                    <ol class="space-y-2 text-zinc-400 text-[12px] leading-relaxed">
                        <li><span class="text-amber-400 font-bold">1.</span> Busca el socio por nombre o DNI</li>
                        <li><span class="text-amber-400 font-bold">2.</span> Selecciónalo de la lista</li>
                        <li><span class="text-amber-400 font-bold">3.</span> Se mostrarán sus faltas pendientes</li>
                        <li><span class="text-amber-400 font-bold">4.</span> Confirma el cobro y genera el recibo</li>
                    </ol>
                </div>
                <div class="bg-gradient-to-br from-red-900/20 to-red-900/5 border border-red-500/20 rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-red-400 font-black uppercase text-xs tracking-widest">Multa por falta</h4>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-[11px]">Monto por falta</span>
                            <span class="text-red-400 font-bold text-[11px]">S/ 60.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-[11px]">Aplica a</span>
                            <span class="text-zinc-300 font-bold text-[11px]">Listas cerradas</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($associates))
            <div class="mb-6 space-y-2">
                @foreach($associates as $socio)
                    <button wire:click="seleccionarSocio({{ $socio->id }})"
                        class="w-full text-left p-4 bg-zinc-800/50 hover:bg-amber-600/20 rounded-xl border border-zinc-700 transition-all uppercase font-black text-white text-xs">
                        {{ $socio->last_name }}, {{ $socio->name }}
                    </button>
                @endforeach
            </div>
        @endif

        @if($asociado_id && isset($multasDetalle))
            <div class="space-y-4">

                @if(count($multasDetalle) > 0)
                    <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">Faltas Pendientes de Cobro</h3>

                    <div class="space-y-2">
                        @foreach($multasDetalle as $multa)
                            <div class="flex items-center justify-between p-3 bg-zinc-800 rounded-2xl border border-red-500/20">
                                <div>
                                    <p class="text-white font-bold text-xs">{{ $multa['evento'] }}</p>
                                    <p class="text-zinc-500 text-[10px]">{{ $multa['fecha'] }}</p>
                                </div>
                                <span class="text-red-400 font-black text-sm">S/ {{ number_format($multa['monto'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-black/40 p-6 rounded-3xl border border-zinc-800">
                        <div class="flex justify-between items-end pt-2">
                            <div>
                                <p class="text-zinc-500 font-bold text-[10px] uppercase">Total a Cobrar</p>
                                <p class="text-5xl text-amber-500 font-black italic">S/ {{ number_format($totalFinal, 2) }}</p>
                                <p class="text-zinc-600 text-[10px] mt-1">{{ $cantidadMultas }} falta(s) × S/ 60.00</p>
                            </div>
                            <button wire:click="confirmarPago"
                                class="bg-amber-600 hover:bg-amber-500 text-white font-black px-8 py-4 rounded-2xl uppercase text-xs">
                                Cobrar Multas
                            </button>
                        </div>
                    </div>

                @else
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-400 font-black uppercase text-sm">Sin multas pendientes</p>
                        <p class="text-zinc-600 text-xs mt-1">Este socio está al día con sus faltas.</p>
                    </div>
                @endif

                @if(!empty($payments) && $payments->count())
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-white uppercase text-xs font-black tracking-widest">Multas Cobradas</h3>
                            <span class="text-zinc-400 text-[10px]">{{ $payments->count() }} registros</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($payments as $pago)
                                <div class="p-4 bg-zinc-800 rounded-3xl border border-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <p class="text-zinc-400 text-[10px] uppercase tracking-[0.25em]">Recibo N° {{ $pago->invoice_number }}</p>
                                        <p class="text-white font-black text-sm">{{ strtoupper($pago->concept) }}</p>
                                        <p class="text-zinc-500 text-[11px]">Fecha: {{ $pago->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-amber-400 text-[11px]">Multas: S/ {{ number_format($pago->fine_amount, 2) }}</p>
                                    </div>
                                    <button wire:click="imprimirPago({{ $pago->id }})"
                                        class="bg-green-600 hover:bg-green-500 text-white uppercase text-[11px] font-black px-5 py-3 rounded-2xl">
                                        Imprimir
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        @endif
    </div>
</div>
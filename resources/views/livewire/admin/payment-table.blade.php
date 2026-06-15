<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-8">
        <div class="p-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-900/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Caja de Cobranza — Cuota Familiar</h2>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] shadow-2xl overflow-hidden p-8">

        {{-- Buscador --}}
        <input type="text" wire:model.live="search" placeholder="Buscar socio por nombre o DNI..."
            class="w-full bg-zinc-800 border-none rounded-2xl py-4 px-6 text-white mb-6 focus:ring-2 focus:ring-blue-600 font-bold">

        {{-- Pantalla de bienvenida --}}
        @if(!$resumenDeuda)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-900/20 to-blue-900/5 border border-blue-500/20 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="text-blue-400 font-black uppercase text-xs tracking-widest">Cómo usar</h4>
                </div>
                <ol class="space-y-2 text-zinc-400 text-[12px] leading-relaxed">
                    <li><span class="text-blue-400 font-bold">1.</span> Busca el socio por nombre o DNI</li>
                    <li><span class="text-blue-400 font-bold">2.</span> Selecciónalo de la lista</li>
                    <li><span class="text-blue-400 font-bold">3.</span> Marca los meses a cobrar</li>
                    <li><span class="text-blue-400 font-bold">4.</span> El sistema calcula el total según la tarifa de cada año</li>
                </ol>
            </div>
            <div class="bg-gradient-to-br from-zinc-800/60 to-zinc-900/20 border border-zinc-700/40 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <h4 class="text-zinc-400 font-black uppercase text-xs tracking-widest">Tarifas por año</h4>
                </div>
                <div class="space-y-1.5">
                    @foreach([['2021 – 2022','S/ 3.00','zinc'],['2023 – 2024','S/ 4.00','zinc'],['2025 – 2026','S/ 10.00','blue']] as [$periodo,$monto,$color])
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500 text-[11px]">{{ $periodo }}</span>
                        <span class="text-{{ $color }}-400 font-black text-[11px]">{{ $monto }}/mes</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Resultados de búsqueda (solo si no hay socio seleccionado) --}}
        @if(!$asociado_id && !empty($associates))
        <div class="mb-6 space-y-2">
            @foreach($associates as $socio)
            <button wire:click="seleccionarSocio({{ $socio->id }})"
                class="w-full text-left p-4 bg-zinc-800/50 hover:bg-blue-600/20 rounded-xl border border-zinc-700 transition-all uppercase font-black text-white text-xs">
                {{ $socio->last_name }}, {{ $socio->name }}
                <span class="text-zinc-500 font-normal normal-case ml-2">DNI {{ $socio->dni }}</span>
            </button>
            @endforeach
        </div>
        @endif

        {{-- Selector de Conexión/Instalación --}}
        @if(!empty($conexiones) && count($conexiones) > 1 && $asociado_id)
        <div class="mb-6 p-4 rounded-2xl bg-cyan-900/10 border border-cyan-500/20 space-y-3">
            <p class="text-cyan-400 font-black text-[10px] uppercase tracking-widest">📍 Instalación seleccionada</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($conexiones as $conn)
                <button wire:click="$set('connection_id', {{ $conn['id'] }})"
                    class="p-3 rounded-xl border-2 transition-all font-bold text-sm uppercase
                        {{ $connection_id === $conn['id'] ? 'bg-cyan-600/20 border-cyan-500 text-cyan-300' : 'bg-zinc-800 border-zinc-700 text-zinc-300 hover:border-cyan-500' }}">
                    {{ $conn['label'] }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        @if($asociado_id && $resumenDeuda)
        <div class="mb-6">
            <button wire:click="$set('asociado_id', null)"
                class="w-full p-3 rounded-xl bg-zinc-700/50 hover:bg-zinc-600/50 border border-zinc-600 text-zinc-300 font-bold text-sm uppercase transition-all">
                ← Cambiar socio o instalación
            </button>
        </div>
        @endif

        {{-- Panel de cobro --}}
        @if($resumenDeuda)
        @if($errorMessage)
        <div class="mb-4 p-4 rounded-2xl bg-rose-900/20 border border-rose-600/20 text-rose-300 text-sm font-bold">
            {{ $errorMessage }}
        </div>
        @endif
        @php
        $mesActualStr = now()->format('Y-m');
        $tieneDeuda = collect($resumenDeuda['items'])->where('adelanto', false)->isNotEmpty();
        $tieneAdelanto = collect($resumenDeuda['items'])->where('adelanto', true)->isNotEmpty();
        $grupos = $resumenDeuda['grupos_tarifa'] ?? [];
        @endphp

        <div class="space-y-5">

            {{-- Control meses adelantados --}}
            <div class="flex items-center justify-between p-4 bg-zinc-800/50 border border-cyan-500/20 rounded-2xl">
                <div>
                    <p class="text-cyan-400 font-black text-[10px] uppercase tracking-widest">Meses futuros (adelanto)</p>
                    <p class="text-zinc-500 text-[10px] mt-0.5">Cuántos meses adelantados mostrar</p>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="decrementarAdelanto"
                        class="w-7 h-7 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-white font-black flex items-center justify-center transition-colors">−</button>
                    <span class="text-white font-black text-lg w-8 text-center">{{ $mesesAdelanto }}</span>
                    <button wire:click="incrementarAdelanto"
                        class="w-7 h-7 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-white font-black flex items-center justify-center transition-colors">+</button>
                </div>
            </div>

            {{-- ── MESES PENDIENTES ── --}}
            @if($tieneDeuda)
            <div>
                <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Meses Pendientes</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-64 overflow-y-auto pr-1">
                    @foreach($resumenDeuda['items'] as $item)
                    @if(!$item['adelanto'])
                    @php $checked = in_array($item['meses'][0], $mesesSeleccionados); @endphp
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl cursor-pointer transition-colors
                                        {{ $checked ? 'bg-blue-600/20 border border-blue-500/40' : 'bg-zinc-800 border border-transparent' }}">
                        <input type="checkbox"
                            wire:click="toggleMes('{{ $item['meses'][0] }}')"
                            {{ $checked ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-blue-600 flex-shrink-0">
                        <div class="min-w-0">
                            <span class="text-white font-bold text-[11px] block leading-tight">{{ $item['etiqueta'] }}</span>
                            <span class="text-zinc-500 text-[9px]">S/ {{ number_format($item['tarifa'], 2) }}</span>
                        </div>
                    </label>
                    @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="flex items-center gap-2 p-4 bg-green-900/10 border border-green-600/20 rounded-2xl">
                <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-400 font-bold text-xs">Socio al día — sin meses pendientes.</p>
            </div>
            @endif

            {{-- ── MESES ADELANTADOS ── --}}
            @if($tieneAdelanto)
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="text-cyan-500 font-black text-[10px] uppercase tracking-widest">Pago por Adelantado</h3>
                    <span class="text-[9px] bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 px-2 py-0.5 rounded-full font-bold uppercase">Futuro</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-52 overflow-y-auto pr-1">
                    @foreach($resumenDeuda['items'] as $item)
                    @if($item['adelanto'])
                    @php $checked = in_array($item['meses'][0], $mesesSeleccionados); @endphp
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl cursor-pointer transition-colors
                                        {{ $checked ? 'bg-cyan-600/20 border border-cyan-500/40' : 'bg-zinc-800/60 border border-cyan-900/30' }}">
                        <input type="checkbox"
                            wire:click="toggleMes('{{ $item['meses'][0] }}')"
                            {{ $checked ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-cyan-500 flex-shrink-0">
                        <div class="min-w-0">
                            <span class="text-white font-bold text-[11px] block leading-tight">{{ $item['etiqueta'] }}</span>
                            <span class="text-cyan-500 text-[9px] font-black">Adelanto · S/ {{ number_format($item['tarifa'], 2) }}</span>
                        </div>
                    </label>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── RESUMEN Y COBRO ── --}}
            <div class="bg-black/40 p-6 rounded-3xl border border-zinc-800 space-y-3">

                {{-- Subtotales por grupo de tarifa --}}
                @if(!empty($grupos) && count($mesesSeleccionados) > 0)
                <div class="space-y-2">
                    <p class="text-zinc-600 font-black text-[9px] uppercase tracking-widest">Desglose por tarifa</p>
                    @foreach($grupos as $grupo)
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-zinc-400">
                            {{ $grupo['cantidad'] }} mes(es) × S/ {{ number_format($grupo['tarifa'], 2) }}

                            @php
                            $anios = collect([2021, 2022, 2023, 2024, 2025, 2026])
                            ->filter(fn($a) => (
                            ($a >= 2013 && $a <= 2022 && $grupo['tarifa']==3.00) ||
                                ($a>= 2023 && $a <= 2024 && $grupo['tarifa']==4.00) ||
                                    ($a>= 2025 && $a <= 2026 && $grupo['tarifa']==10.00)
                                        ));

                                        $label=$grupo['tarifa']==3.00 ? '2013–2022'
                                        : ($grupo['tarifa']==4.00 ? '2023–2024'
                                        : '2025–2026' );
                                        @endphp
                                        <span class="text-zinc-600">({{ $label }})</span>
                        </span>
                        <span class="text-white font-bold">S/ {{ number_format($grupo['subtotal'], 2) }}</span>
                    </div>
                    @endforeach

                    @if(count($grupos) > 1)
                    <div class="flex justify-between items-center text-[11px] pt-2 border-t border-zinc-800">
                        <span class="text-zinc-400">Subtotal cuotas</span>
                        <span class="text-white font-bold">S/ {{ number_format($resumenDeuda['subtotal'] ?? 0, 2) }}</span>
                    </div>
                    @endif
                </div>
                <hr class="border-zinc-800">
                @endif

                {{-- Mora --}}
                @if(($resumenDeuda['mora_calculada'] ?? 0) > 0)
                <div class="flex justify-between items-center">
                    <label class="flex items-center gap-2 text-white font-bold cursor-pointer text-xs">
                        <input type="checkbox" wire:model.live="aplicarMora"
                            class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-blue-600">
                        Aplicar mora
                    </label>
                    <span class="text-amber-400 font-bold text-sm">
                        + S/ {{ number_format($resumenDeuda['mora_calculada'], 2) }}
                    </span>
                </div>
                @endif

                {{-- Monto personalizado --}}
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer w-fit">
                        <input type="checkbox" wire:model.live="usarMontoPersonalizado"
                            class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-violet-500">
                        <span class="text-zinc-400 font-bold text-xs">Ajustar monto manualmente</span>
                    </label>

                    @if($usarMontoPersonalizado)
                    <div class="mt-2 p-3 bg-violet-900/10 border border-violet-500/30 rounded-2xl space-y-1.5">
                        <p class="text-violet-400 text-[10px] font-black uppercase tracking-widest">
                            Monto acordado con el socio
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400 font-bold text-sm">S/</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model.live="montoPersonalizado"
                                placeholder="0.00"
                                class="flex-1 bg-zinc-800 border border-violet-500/40 rounded-xl px-3 py-2 text-white font-black text-lg focus:outline-none focus:border-violet-400">
                        </div>
                        <p class="text-zinc-600 text-[10px]">
                            El desglose histórico se conserva como referencia.
                            Monto calculado: S/ {{ number_format($resumenDeuda['subtotal'] ?? 0, 2) }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Total y botón --}}
                <div class="flex justify-between items-end pt-2 border-t border-zinc-800">
                    <div>
                        <p class="text-zinc-500 font-bold text-[10px] uppercase">Total a Cobrar</p>
                        <p class="text-5xl font-black italic leading-none mt-1
                                {{ $usarMontoPersonalizado ? 'text-violet-400' : 'text-green-500' }}">
                            S/ {{ number_format($totalFinal, 2) }}
                        </p>
                        @if($usarMontoPersonalizado)
                        <p class="text-violet-500/70 text-[10px] mt-1 font-bold">● Monto ajustado manualmente</p>
                        @endif
                        @if(count($mesesSeleccionados) === 0)
                        <p class="text-zinc-600 text-[10px] mt-1">Selecciona al menos un mes</p>
                        @endif
                    </div>
                    <button wire:click="confirmarPago" wire:loading.attr="disabled"
                        @if(count($mesesSeleccionados)===0) disabled @endif
                        class="bg-blue-600 hover:bg-blue-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black px-8 py-4 rounded-2xl uppercase text-xs transition-colors">
                        Cobrar
                    </button>
                </div>
            </div>

            {{-- Historial --}}
            @if(!empty($payments) && $payments->count())
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-zinc-500 uppercase text-[10px] font-black tracking-widest">Pagos Realizados</h3>
                    <span class="text-zinc-600 text-[10px]">{{ $payments->count() }} registros</span>
                </div>
                @foreach($payments as $pago)
                <div class="p-4 bg-zinc-800 rounded-3xl border border-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <p class="text-zinc-500 text-[10px] uppercase tracking-[0.2em]">Recibo N° {{ $pago->invoice_number }}</p>
                        <p class="text-white font-black text-xs">{{ strtoupper($pago->concept) }}</p>
                        <p class="text-zinc-600 text-[10px]">{{ $pago->created_at->format('d/m/Y H:i') }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-green-400 font-bold text-[11px]">S/ {{ number_format($pago->amount, 2) }}</span>
                            @if($pago->late_fee_applied > 0)
                            <span class="text-amber-400 text-[10px]">+ mora S/ {{ number_format($pago->late_fee_applied, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    <button wire:click="imprimirPago({{ $pago->id }})"
                        class="bg-zinc-700 hover:bg-green-600 text-white uppercase text-[11px] font-black px-5 py-3 rounded-2xl transition-colors flex-shrink-0">
                        Imprimir
                    </button>
                </div>
                @endforeach
            </div>
            @endif

        </div>
        @endif
    </div>
</div>
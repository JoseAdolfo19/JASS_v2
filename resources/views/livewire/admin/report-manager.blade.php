<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex flex-col lg:flex-row gap-8">
        
        

        {{-- SECCIONES DE REPORTES --}}
        <div class="w-full lg:w-2/3 space-y-8">

            {{-- 1. PADRÓN GENERAL DE MOROSOS --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-white font-black uppercase italic text-lg">Padrón General de Morosos</h3>
                        <p class="text-zinc-500 text-[10px] font-bold">Lista automatizada de socios con pagos pendientes</p>
                    </div>
                    <button wire:click="exportMorososPDF" class="bg-red-600 hover:bg-red-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($morososData->take(6) as $item)
                        <div class="bg-zinc-800/50 p-4 rounded-xl">
                            <div class="text-white font-bold text-sm">{{ $item['associate']->last_name }}, {{ $item['associate']->name }}</div>
                            <div class="text-zinc-400 text-xs">{{ $item['associate']->sector->name ?? 'Sin sector' }}</div>
                            <div class="text-red-400 font-black text-lg mt-2">{{ $item['meses_deuda'] }} meses</div>
                            <div class="text-green-400 font-black">S/ {{ number_format($item['total'], 2) }}</div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-8 text-zinc-500">
                            ¡Excelente! No hay morosos pendientes.
                        </div>
                        @endforelse
                    </div>
                    @if($morososData->count() > 6)
                    <div class="text-center mt-4">
                        <span class="text-zinc-500 text-xs">Mostrando 6 de {{ $morososData->count() }} morosos</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. BALANCE DE CAJA --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-white font-black uppercase italic text-lg">Balance de Caja</h3>
                        <p class="text-zinc-500 text-[10px] font-bold">Estado financiero simple y transparente</p>
                    </div>
                    <button wire:click="exportBalancePDF" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-green-400 text-3xl font-black">S/ {{ number_format($balanceData['ingresos'], 2) }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Ingresos Totales</div>
                        </div>
                        <div class="text-center">
                            <div class="text-red-400 text-3xl font-black">S/ {{ number_format($balanceData['egresos'], 2) }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Egresos Totales</div>
                        </div>
                        <div class="text-center">
                            <div class="text-blue-400 text-3xl font-black">S/ {{ number_format($balanceData['saldo'], 2) }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Saldo Disponible</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. REPORTE DE ALTAS Y BAJAS --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-white font-black uppercase italic text-lg">Altas y Bajas</h3>
                        <p class="text-zinc-500 text-[10px] font-bold">Registro de variaciones en el padrón de socios</p>
                    </div>
                    <button wire:click="exportAltasBajasPDF" class="bg-purple-600 hover:bg-purple-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-green-400 text-4xl font-black">{{ $altasBajasData['altas'] }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Nuevos Inscritos</div>
                            <div class="text-zinc-500 text-xs">en {{ date('Y') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-red-400 text-4xl font-black">{{ $altasBajasData['bajas'] }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Socios Retirados</div>
                            <div class="text-zinc-500 text-xs">en {{ date('Y') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-blue-400 text-4xl font-black">{{ $altasBajasData['crecimiento_neto'] }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Crecimiento Neto</div>
                            <div class="text-zinc-500 text-xs">Altas - Bajas</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. RESUMEN DE DEUDA POR MULTAS --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-white font-black uppercase italic text-lg">Deuda por Multas</h3>
                        <p class="text-zinc-500 text-[10px] font-bold">Separación de ingresos por conceptos especiales</p>
                    </div>
                    <button wire:click="exportMultasPDF" class="bg-orange-600 hover:bg-orange-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button>
                </div>
                <div class="p-6">
                    @if($multasData->count() > 0)
                    <div class="space-y-3">
                        @foreach($multasData->take(5) as $item)
                        <div class="flex justify-between items-center bg-zinc-800/50 p-3 rounded-xl">
                            <div>
                                <div class="text-white font-bold text-sm">{{ $item['associate']->last_name }}, {{ $item['associate']->name }}</div>
                                <div class="text-zinc-400 text-xs">{{ $item['cantidad_multas'] }} multa(s)</div>
                            </div>
                            <div class="text-orange-400 font-black text-lg">S/ {{ number_format($item['total_multas'], 2) }}</div>
                        </div>
                        @endforeach
                        @if($multasData->count() > 5)
                        <div class="text-center text-zinc-500 text-xs">
                            Y {{ $multasData->count() - 5 }} socio(s) más...
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8 text-zinc-500">
                        No hay multas registradas
                    </div>
                    @endif
                </div>
            </div>

            {{-- 5. LISTA DE "APTOS PARA CORTE" --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-red-400 font-black uppercase italic text-lg">Aptos para Corte</h3>
                        <p class="text-zinc-500 text-[10px] font-bold">Alerta de morosidad extrema</p>
                    </div>
                    <button wire:click="exportAptosCortePDF" class="bg-red-600 hover:bg-red-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button>
                </div>
                <div class="p-6">
                    @if($aptosCorteData->count() > 0)
                    <div class="bg-red-900/20 border border-red-600/30 p-4 rounded-xl mb-4">
                        <div class="text-red-400 font-black text-sm uppercase">⚠️ Alerta Crítica</div>
                        <div class="text-zinc-300 text-xs">{{ $aptosCorteData->count() }} socio(s) superan los 6 meses de deuda</div>
                    </div>
                    <div class="space-y-3">
                        @foreach($aptosCorteData->take(5) as $item)
                        <div class="flex justify-between items-center bg-red-900/10 border border-red-600/20 p-3 rounded-xl">
                            <div>
                                <div class="text-white font-bold text-sm">{{ $item['associate']->last_name }}, {{ $item['associate']->name }}</div>
                                <div class="text-zinc-400 text-xs">{{ $item['associate']->sector->name ?? 'Sin sector' }}</div>
                            </div>
                            <div class="text-red-400 font-black text-lg">{{ $item['meses_deuda'] }} meses</div>
                        </div>
                        @endforeach
                        @if($aptosCorteData->count() > 5)
                        <div class="text-center text-zinc-500 text-xs">
                            Y {{ $aptosCorteData->count() - 5 }} socio(s) más aptos para corte...
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center text-zinc-500 text-xs">
                        ¡Excelente! No hay socios aptos para corte.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
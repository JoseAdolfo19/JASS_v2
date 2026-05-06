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
                            <div class="text-white font-bold text-sm">{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</div>
                            <div class="text-zinc-400 text-xs">{{ $item['associate']['sector'] ?? 'Sin sector' }}</div>
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
                    <!-- <button wire:click="exportAltasBajasPDF" class="bg-purple-600 hover:bg-purple-500 text-white font-black px-4 py-2 rounded-xl uppercase text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </button> -->
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                            <div class="text-yellow-400 text-4xl font-black">{{ $altasBajasData['suspendidos'] ?? 0 }}</div>
                            <div class="text-zinc-400 text-sm font-bold uppercase">Socios Suspendidos</div>
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
                        <h3 class="text-white font-black uppercase italic text-lg">Deuda por Multas de Faenas y Asambleas</h3>
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
                                <div class="text-white font-bold text-sm">{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</div>
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
                        <h3 class="text-red-400 font-black uppercase italic text-lg">Aptos para Corte de Servicios</h3>
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
                        <div class="text-red-400 font-black text-sm uppercase">Alerta Crítica</div>
                        <div class="text-zinc-300 text-xs">{{ $aptosCorteData->count() }} socio(s) superan los 6 meses de deuda</div>
                    </div>
                    <div class="space-y-3">
                        @foreach($aptosCorteData->take(5) as $item)
                        <div class="flex justify-between items-center bg-red-900/10 border border-red-600/20 p-3 rounded-xl">
                            <div>
                                <div class="text-white font-bold text-sm">{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</div>
                                <div class="text-zinc-400 text-xs">{{ $item['associate']['sector'] ?? 'Sin sector' }}</div>
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
        {{-- PANEL LATERAL --}}
        <div class="w-full lg:w-1/3 space-y-6">
            {{-- ESTADÍSTICAS RÁPIDAS --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl p-6">
                <h3 class="text-white font-black uppercase italic text-lg mb-4">Estadísticas Rápidas</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Morosos Activos</div>
                                <div class="text-zinc-400 text-xs">Socios con deuda</div>
                            </div>
                        </div>
                        <div class="text-red-400 font-black text-xl">{{ $morososData->count() }}</div>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Saldo Disponible</div>
                                <div class="text-zinc-400 text-xs">Balance actual</div>
                            </div>
                        </div>
                        <div class="text-green-400 font-black text-xl">S/ {{ number_format($balanceData['saldo'], 2) }}</div>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Nuevos Inscritos</div>
                                <div class="text-zinc-400 text-xs">Este año</div>
                            </div>
                        </div>
                        <div class="text-blue-400 font-black text-xl">{{ $altasBajasData['altas'] }}</div>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Multas Pendientes</div>
                                <div class="text-zinc-400 text-xs">Deuda por multas</div>
                            </div>
                        </div>
                        <div class="text-orange-400 font-black text-xl">{{ $multasData->count() }}</div>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Deuda Pendiente</div>
                                <div class="text-zinc-400 text-xs">Total por cobrar a morosos</div>
                            </div>
                        </div>
                        <div class="text-yellow-400 font-black text-xl">S/ {{ number_format($morososData->sum('total'), 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- ACCIONES RÁPIDAS --}}
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl p-6">
                <h3 class="text-white font-black uppercase italic text-lg mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <button wire:click="exportAllReportsPDF" class="w-full bg-red-600 hover:bg-red-500 text-white font-black px-4 py-3 rounded-xl uppercase text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar Todo
                    </button>
                    <button wire:click="viewDetails" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black px-4 py-3 rounded-xl uppercase text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ver Detalles
                    </button>
                    <button wire:click="configureAlerts" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-black px-4 py-3 rounded-xl uppercase text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Configurar Alertas
                    </button>
                </div>
            </div>
            @if($showReportDetails)
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-white font-black uppercase text-sm">Detalle del reporte</h4>
                        <p class="text-zinc-500 text-xs">Resumen instantáneo de métricas clave</p>
                    </div>
                    <button wire:click="hideQuickPanel" class="text-zinc-400 hover:text-white text-xs font-bold">Cerrar</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <span class="text-zinc-300 text-sm">Total socios con deuda</span>
                        <span class="text-red-400 font-black">{{ $morososData->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <span class="text-zinc-300 text-sm">Corte de servicios en 6+ meses</span>
                        <span class="text-red-400 font-black">{{ $aptosCorteData->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-zinc-800/50 p-4 rounded-xl">
                        <span class="text-zinc-300 text-sm">Suspendidos</span>
                        <span class="text-yellow-400 font-black">{{ $altasBajasData['suspendidos'] }}</span>
                    </div>
                </div>
            </div>
            @endif
            @if($showAlertConfig)
            <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-white font-black uppercase text-sm">Configuración de alertas</h4>
                        <p class="text-zinc-500 text-xs">Ajusta avisos rápidos y notificaciones</p>
                    </div>
                    <button wire:click="hideQuickPanel" class="text-zinc-400 hover:text-white text-xs font-bold">Cerrar</button>
                </div>
                <div class="space-y-3">
                    <div class="bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold text-sm">Alerta de morosos</div>
                                <div class="text-zinc-400 text-xs">Activa avisos si supera 20 morosos</div>
                            </div>
                            <div class="text-green-400 font-black text-sm">Activo</div>
                        </div>
                    </div>
                    <div class="bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold text-sm">Alerta de corte</div>
                                <div class="text-zinc-400 text-xs">Cuando hay 5+ socios aptos para corte</div>
                            </div>
                            <div class="text-green-400 font-black text-sm">Activo</div>
                        </div>
                    </div>
                    <div class="bg-zinc-800/50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold text-sm">Última actualización</div>
                                <div class="text-zinc-400 text-xs">{{ now()->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-zinc-400 text-sm">Sincronizado</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
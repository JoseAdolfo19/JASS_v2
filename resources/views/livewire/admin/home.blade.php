<div class="max-w-7xl mx-auto py-8 px-4">
    {{-- ENCABEZADO --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-white italic tracking-tighter uppercase">Panel de Control</h1>
        <p class="text-zinc-500 font-bold uppercase text-xs tracking-widest mt-1">Gestión Administrativa J.A.S.S. 2026</p>
    </div>

    {{-- TARJETAS DE MÉTRICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Total Recaudado --}}
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-green-600/20 rounded-2xl text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">Recaudación Total</span>
            </div>
            <p class="text-3xl font-black text-white italic">S/ {{ number_format($totalRecaudado, 2) }}</p>
        </div>

        {{-- Socios Morosos --}}
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-red-600/20 rounded-2xl text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">Socios con Deuda</span>
            </div>
            <p class="text-3xl font-black text-white italic">{{ $cantidadMorosos }} <small class="text-xs text-zinc-600">SOCIOS</small></p>
        </div>

        {{-- Gastos del Mes --}}
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-orange-600/20 rounded-2xl text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">Gastos Abril</span>
            </div>
            <p class="text-3xl font-black text-white italic text-orange-400">S/ 0.00</p>
        </div>

        {{-- Saldo en Caja --}}
        <div class="bg-blue-600 border border-blue-500 p-6 rounded-[2rem] shadow-xl shadow-blue-900/20">
            <div class="flex items-center gap-4 mb-4 text-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                <span class="font-black text-[10px] uppercase tracking-widest">Saldo Disponible</span>
            </div>
            <p class="text-3xl font-black text-white italic italic">S/ {{ number_format($totalRecaudado - 0, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ACCESOS RÁPIDOS --}}
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest ml-4">Acciones Críticas</h3>
            
            <a href="{{ route('admin.pagos') }}" class="flex items-center justify-between p-6 bg-zinc-900 border border-zinc-800 rounded-3xl hover:border-blue-500 hover:bg-blue-600/5 transition-all group">
                <span class="text-white font-black uppercase text-xs italic tracking-tight">Cobrar Cuotas</span>
                <svg class="w-5 h-5 text-zinc-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>

            <a href="{{ route('admin.historial-pagos') }}" class="flex items-center justify-between p-6 bg-zinc-900 border border-zinc-800 rounded-3xl hover:border-blue-500 hover:bg-blue-600/5 transition-all group">
                <span class="text-white font-black uppercase text-xs italic tracking-tight">Ver Historial</span>
                <svg class="w-5 h-5 text-zinc-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>

            <div class="p-8 bg-zinc-800/30 border border-zinc-800 rounded-[2rem] mt-6">
                <h4 class="text-white font-black uppercase italic mb-2">Configuración</h4>
                <p class="text-zinc-500 text-[10px] font-bold leading-relaxed">Ajusta la tarifa mensual y el monto de la mora desde el panel de ajustes.</p>
                <button class="mt-4 w-full py-3 bg-zinc-800 rounded-xl text-white font-black text-[10px] uppercase tracking-widest hover:bg-zinc-700 transition-all">Ir a Ajustes</button>
            </div>
        </div>

        {{-- ÚLTIMOS MOVIMIENTOS --}}
        <div class="lg:col-span-2">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] overflow-hidden">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-white font-black uppercase italic tracking-tighter">Últimos Pagos Realizados</h3>
                </div>
                <div class="divide-y divide-zinc-800">
                    @foreach($ultimosPagos as $pago)
                    <div class="p-6 flex items-center justify-between hover:bg-white/5 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 font-black text-xs">
                                {{ substr($pago->associate->last_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white font-black uppercase text-xs tracking-tight">{{ $pago->associate->last_name }}, {{ $pago->associate->name }}</p>
                                <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest">Recibo #{{ $pago->invoice_number }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-green-500 font-black italic">S/ {{ number_format($pago->amount, 2) }}</p>
                            <p class="text-[9px] text-zinc-600 font-bold uppercase">{{ $pago->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="p-4 bg-zinc-800/30 text-center">
                    <a href="{{ route('admin.historial-pagos') }}" class="text-zinc-500 font-black text-[10px] uppercase hover:text-white transition-all">Ver todo el libro caja</a>
                </div>
            </div>
        </div>
    </div>
</div>
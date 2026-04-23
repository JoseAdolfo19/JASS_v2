<div class="max-w-7xl mx-auto py-8 px-4">
    {{-- ENCABEZADO --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-white italic tracking-tighter uppercase">Panel de Control</h1>
        <p class="text-zinc-500 font-bold uppercase text-xs tracking-widest mt-1">Gestión Administrativa J.A.S.S. 2026</p>
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
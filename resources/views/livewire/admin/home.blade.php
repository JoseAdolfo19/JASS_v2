<div class="max-w-7xl mx-auto py-8 px-4">
    {{-- ENCABEZADO --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-white italic tracking-tighter uppercase">Panel de Control</h1>
        <p class="text-zinc-500 font-bold uppercase text-xs tracking-widest mt-1">Gestión Administrativa J.A.S.S. 2026</p>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
        {{-- Número de Socios --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <h4 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Socios</h4>
            <p class="text-white font-black text-2xl">{{ $numeroSocios }}</p>
        </div>

        {{-- Número de Deudores --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h4 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Deudores</h4>
            <p class="text-white font-black text-2xl">{{ $numeroDeudores }}</p>
        </div>

        {{-- Ingresos --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h4 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Ingresos</h4>
            <p class="text-green-500 font-black text-2xl">S/ {{ number_format($ingresos, 2) }}</p>
        </div>

        {{-- Egresos --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <h4 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Egresos</h4>
            <p class="text-red-500 font-black text-2xl">S/ {{ number_format($egresos, 2) }}</p>
        </div>

        {{-- Saldo --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h4 class="text-zinc-500 font-black text-[10px] uppercase tracking-widest mb-2">Saldo</h4>
            <p class="text-{{ $saldo >= 0 ? 'green' : 'red' }}-500 font-black text-2xl">S/ {{ number_format($saldo, 2) }}</p>
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
                <a href="{{ route('admin.settings') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white font-bold uppercase rounded-full hover:bg-blue-600 transition-all">Ir a Ajustes</a>
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
                    <a href="{{ route('admin.historial-pagos') }}" class="text-zinc-500 font-black text-[10px] uppercase hover:text-white transition-all">Ver todo el Historial de Pagos</a>
                </div>
            </div>
        </div>
    </div>
</div>
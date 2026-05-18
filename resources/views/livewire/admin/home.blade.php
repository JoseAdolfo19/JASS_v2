<div class="max-w-7xl mx-auto py-8 px-4">

    {{-- ── ENCABEZADO ── --}}
    <div class="mb-10 flex items-end justify-between">
        <div>
            <p class="text-zinc-600 font-bold uppercase text-[10px] tracking-[0.3em] mb-1">
                {{ now()->translatedFormat('l, d \d\e F Y') }}
            </p>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase italic leading-none">
                Panel de Control
            </h1>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">
                J.A.S.S. Huayoccary — Gestión Administrativa 2026
            </p>
        </div>
        <a href="{{ route('admin.pagos') }}"
            class="hidden md:flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase px-5 py-3 rounded-2xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Cobrar Cuota
        </a>
    </div>

    {{-- ── ESTADÍSTICAS PRINCIPALES ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

        {{-- Socios --}}
        <div class="col-span-1 bg-zinc-900 border border-zinc-800 rounded-3xl p-5 flex flex-col gap-3 hover:border-zinc-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-blue-600/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Socios activos</p>
                <p class="text-white font-black text-3xl leading-none mt-0.5">{{ $numeroSocios }}</p>
            </div>
        </div>

        {{-- Deudores --}}
        <div class="col-span-1 bg-zinc-900 border border-zinc-800 rounded-3xl p-5 flex flex-col gap-3 hover:border-zinc-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-red-600/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Deudores</p>
                <p class="text-red-400 font-black text-3xl leading-none mt-0.5">{{ $numeroDeudores }}</p>
            </div>
        </div>

        {{-- Ingresos --}}
        <div class="col-span-1 bg-zinc-900 border border-zinc-800 rounded-3xl p-5 flex flex-col gap-3 hover:border-zinc-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-green-600/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Ingresos</p>
                <p class="text-green-400 font-black text-2xl leading-none mt-0.5">S/ {{ number_format($ingresos, 2) }}</p>
            </div>
        </div>

        {{-- Egresos --}}
        <div class="col-span-1 bg-zinc-900 border border-zinc-800 rounded-3xl p-5 flex flex-col gap-3 hover:border-zinc-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-orange-600/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                </svg>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Egresos</p>
                <p class="text-orange-400 font-black text-2xl leading-none mt-0.5">S/ {{ number_format($egresos, 2) }}</p>
            </div>
        </div>

        {{-- Saldo --}}
        <div class="col-span-2 lg:col-span-1 rounded-3xl p-5 flex flex-col gap-3
            {{ $saldo >= 0
                ? 'bg-green-600/10 border border-green-600/30'
                : 'bg-red-600/10 border border-red-600/30' }}">
            <div class="w-9 h-9 rounded-xl {{ $saldo >= 0 ? 'bg-green-600/20' : 'bg-red-600/20' }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $saldo >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                </svg>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Saldo neto</p>
                <p class="font-black text-2xl leading-none mt-0.5 {{ $saldo >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    S/ {{ number_format(abs($saldo), 2) }}
                </p>
                <p class="text-[10px] font-bold mt-1 {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $saldo >= 0 ? '▲ superávit' : '▼ déficit' }}
                </p>
            </div>
        </div>

    </div>

    {{-- ── CUERPO PRINCIPAL ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── ACCESOS RÁPIDOS ── --}}
        <div class="space-y-3">
            <p class="text-zinc-600 font-black text-[10px] uppercase tracking-[0.25em] px-1">Accesos rápidos</p>

            @php
            $accesos = [
                ['route' => 'admin.pagos',          'label' => 'Cobrar Cuota Familiar',     'color' => 'blue',   'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'admin.multas',         'label' => 'Cobrar Multas y Faltas',    'color' => 'amber',  'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z'],
                ['route' => 'admin.otros',          'label' => 'Cuotas Extraordinarias',    'color' => 'purple', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['route' => 'admin.egresos',        'label' => 'Registrar Egreso',          'color' => 'orange', 'icon' => 'M17 13l-5 5m0 0l-5-5m5 5V6'],
                ['route' => 'admin.asistencia',     'label' => 'Pasar Lista / Asistencia',  'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route' => 'admin.historial-pagos','label' => 'Historial de Pagos',        'color' => 'zinc',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['route' => 'admin.reportes',       'label' => 'Ver Reportes',              'color' => 'zinc',   'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
            @endphp

            @foreach($accesos as $acceso)
            <a href="{{ route($acceso['route']) }}"
                class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800
                       hover:border-{{ $acceso['color'] }}-500/50 hover:bg-{{ $acceso['color'] }}-600/5
                       rounded-2xl transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-{{ $acceso['color'] }}-600/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-{{ $acceso['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $acceso['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xs uppercase tracking-tight">{{ $acceso['label'] }}</span>
                </div>
                <svg class="w-4 h-4 text-zinc-700 group-hover:text-zinc-400 transition-colors flex-shrink-0"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>

        {{-- ── ÚLTIMOS PAGOS ── --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                    <h3 class="text-white font-black uppercase italic text-sm tracking-tight">Últimos Pagos</h3>
                    <a href="{{ route('admin.historial-pagos') }}"
                        class="text-zinc-600 hover:text-zinc-300 font-black text-[10px] uppercase tracking-widest transition-colors">
                        Ver todo →
                    </a>
                </div>

                <div class="divide-y divide-zinc-800/60 flex-1">
                    @forelse($ultimosPagos as $pago)
                    <div class="px-6 py-3.5 flex items-center justify-between gap-4 hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Avatar inicial --}}
                            <div class="w-8 h-8 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-black text-[11px] flex-shrink-0">
                                {{ strtoupper(substr($pago->associate->last_name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-bold text-xs uppercase truncate leading-snug">
                                    {{ $pago->associate->last_name }}, {{ $pago->associate->name }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-wider">
                                        #{{ $pago->invoice_number }}
                                    </span>
                                    @if($pago->type === 'falta')
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 uppercase">multa</span>
                                    @elseif($pago->type === 'cuota')
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 uppercase">cuota</span>
                                    @else
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-purple-500/10 text-purple-400 uppercase">{{ $pago->type }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-green-400 font-black text-sm italic">
                                S/ {{ number_format($pago->amount, 2) }}
                            </p>
                            <p class="text-[9px] text-zinc-600 font-bold">
                                {{ $pago->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-zinc-600 text-sm font-bold uppercase italic">Sin pagos registrados aún</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ── ALERTAS / DEUDORES ── --}}
            @if($numeroDeudores > 0)
            <div class="bg-red-950/30 border border-red-800/30 rounded-[2rem] p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-red-600/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-red-300 font-black text-sm uppercase">
                            {{ $numeroDeudores }} {{ $numeroDeudores === 1 ? 'socio con deuda pendiente' : 'socios con deuda pendiente' }}
                        </p>
                        <p class="text-red-500/70 text-xs font-bold mt-0.5">
                            Revisa el módulo de cobro para gestionar los pagos atrasados.
                        </p>
                    </div>
                    <a href="{{ route('admin.pagos') }}"
                        class="flex-shrink-0 bg-red-600 hover:bg-red-500 text-white font-black text-[10px] uppercase px-4 py-2 rounded-xl transition-colors">
                        Gestionar
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
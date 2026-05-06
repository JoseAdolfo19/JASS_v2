<div class="max-w-4xl mx-auto py-8 px-4 space-y-8">

    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-white font-black uppercase italic text-2xl">Panel de Configuración</h2>
                <p class="text-zinc-500 text-xs font-bold">Tarifas y datos institucionales de la JASS</p>
            </div>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/80 px-4 py-3 text-right">
                <div class="text-zinc-400 text-[10px] uppercase tracking-[.2em] font-bold">Última actualización</div>
                <div class="text-white font-black text-sm">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-[2rem] border border-zinc-800 bg-zinc-900/80 p-5 shadow-xl">
                <div class="text-zinc-400 text-[10px] uppercase font-bold tracking-[.18em] mb-3">Cuota mensual</div>
                <div class="text-white text-3xl font-black">S/ {{ number_format((float)$cuota_mensual, 2) }}</div>
                <p class="text-zinc-500 text-xs mt-2">Valor que usa el sistema para el cálculo mensual.</p>
            </div>
            <div class="rounded-[2rem] border border-zinc-800 bg-zinc-900/80 p-5 shadow-xl">
                <div class="text-zinc-400 text-[10px] uppercase font-bold tracking-[.18em] mb-3">Mora</div>
                <div class="text-white text-3xl font-black">S/ {{ number_format((float)$mora_monto, 2) }}</div>
                <p class="text-white text-3xl font-black">Se aplica cada {{ $mora_meses }} meses sin pago.</p>
            </div>
        </div>
    </div>

    @if(session('tarifas_ok') || session('jass_ok'))
    <div class="rounded-3xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-100 shadow-inner">
        @if(session('tarifas_ok'))
            <div>{{ session('tarifas_ok') }}</div>
        @endif
        @if(session('jass_ok'))
            <div>{{ session('jass_ok') }}</div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-zinc-800">
                <h3 class="text-white font-black uppercase italic text-lg">Tarifas del Servicio</h3>
                <p class="text-zinc-500 text-[10px] font-bold">Estos valores se aplican automáticamente en todos los cálculos del sistema.</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="bg-zinc-800/40 rounded-2xl p-5 border border-zinc-700/50">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="lg:flex-1">
                            <label class="text-white font-bold text-sm block mb-1">Cuota Mensual</label>
                            <p class="text-zinc-500 text-xs mb-3">Monto que paga cada socio por mes de servicio de agua.</p>
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-400 font-bold text-sm">S/</span>
                                <input wire:model="cuota_mensual" type="number" step="0.01" min="0"
                                    class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-2.5 text-sm w-36 focus:outline-none focus:border-blue-500">
                            </div>
                            @error('cuota_mensual') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-right">
                            <div class="text-green-400 text-3xl font-black">S/ {{ number_format((float)$cuota_mensual, 2) }}</div>
                            <div class="text-zinc-500 text-xs">valor actual</div>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-800/40 rounded-2xl p-5 border border-zinc-700/50">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="lg:flex-1">
                            <label class="text-white font-bold text-sm block mb-1">Mora por Atraso</label>
                            <p class="text-zinc-500 text-xs mb-3">Penalidad que se cobra cuando el socio acumula <span class="text-white font-bold">{{ $mora_meses }} meses</span> de deuda.</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Monto de mora</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-zinc-400 font-bold text-sm">S/</span>
                                        <input wire:model="mora_monto" type="number" step="0.01" min="0"
                                            class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:border-blue-500">
                                    </div>
                                    @error('mora_monto') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="text-zinc-400 text-xs font-bold uppercase block mb-1">Meses para aplicar</label>
                                    <div class="flex items-center gap-2">
                                        <input wire:model="mora_meses" type="number" min="1" max="24"
                                            class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:border-blue-500">
                                        <span class="text-zinc-400 font-bold text-sm">meses</span>
                                    </div>
                                    @error('mora_meses') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-red-400 text-3xl font-black">S/ {{ number_format((float)$mora_monto, 2) }}</div>
                            <div class="text-zinc-500 text-xs">c/ {{ $mora_meses }} meses</div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-900/10 border border-blue-600/20 rounded-2xl p-4">
                    <div class="text-blue-400 font-black text-xs uppercase mb-2">📊 Simulador de deuda</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center">
                        @php
                        $cuota = (float)$cuota_mensual;
                        $mora = (float)$mora_monto;
                        $meses = (int)$mora_meses;
                        @endphp
                        @foreach([3, 6, 12] as $m)
                        @php
                        $bloques = $meses > 0 ? floor($m / $meses) : 0;
                        $moraTotal = $bloques * $mora;
                        $total = ($cuota * $m) + $moraTotal;
                        @endphp
                        <div class="bg-zinc-800/50 rounded-xl p-3">
                            <div class="text-zinc-400 text-xs font-bold">{{ $m }} meses</div>
                            <div class="text-white font-black text-lg">S/ {{ number_format($total, 2) }}</div>
                            <div class="text-zinc-500 text-[10px]">cuota + mora</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button wire:click="saveTarifas" wire:loading.attr="disabled"
                    class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-3 rounded-xl uppercase text-sm transition-colors">
                    <span wire:loading.remove wire:target="saveTarifas">Guardar Tarifas</span>
                    <span wire:loading wire:target="saveTarifas">Guardando...</span>
                </button>
            </div>
        </div>

        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-zinc-800 overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-zinc-800">
                <h3 class="text-white font-black uppercase italic text-lg">Datos de la JASS</h3>
                <p class="text-zinc-500 text-[10px] font-bold">Información institucional que aparece en documentos y reportes.</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-white font-bold text-sm block mb-1">Nombre de la JASS</label>
                        <input wire:model="jass_nombre" type="text"
                            class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-3 w-full text-sm focus:outline-none focus:border-blue-500">
                        @error('jass_nombre') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-white font-bold text-sm block mb-1">Dirección</label>
                        <input wire:model="jass_direccion" type="text"
                            class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-3 w-full text-sm focus:outline-none focus:border-blue-500">
                        @error('jass_direccion') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-white font-bold text-sm block mb-1">Presidente</label>
                            <input wire:model="jass_presidente" type="text"
                                class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-3 w-full text-sm focus:outline-none focus:border-blue-500">
                            @error('jass_presidente') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-white font-bold text-sm block mb-1">Tesorero</label>
                            <input wire:model="jass_tesorero" type="text"
                                class="bg-zinc-800 border border-zinc-600 text-white rounded-xl px-4 py-3 w-full text-sm focus:outline-none focus:border-blue-500">
                            @error('jass_tesorero') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button wire:click="saveJass" wire:loading.attr="disabled"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-black py-3 rounded-xl uppercase text-sm transition-colors">
                    <span wire:loading.remove wire:target="saveJass">Guardar Datos</span>
                    <span wire:loading wire:target="saveJass">Guardando...</span>
                </button>
            </div>
        </div>

    </div>
</div>
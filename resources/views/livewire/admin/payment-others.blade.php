<div class="max-w-4xl mx-auto py-8 px-4 space-y-8">

    {{-- ENCABEZADO --}}
    <div class="flex items-center gap-3">
        <div class="p-2 bg-purple-600 rounded-xl shadow-lg shadow-purple-900/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Cuotas Extraordinarias</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ================================================================
             PANEL IZQUIERDO — COBRO AL SOCIO
        ================================================================ --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl p-6 space-y-5">
            <h3 class="text-zinc-400 font-black text-[10px] uppercase tracking-widest">Cobrar a Socio</h3>

            {{-- Buscador --}}
            <input type="text" wire:model.live="search"
                placeholder="Buscar socio por nombre o DNI..."
                class="w-full bg-zinc-800 border-none rounded-2xl py-3 px-5 text-white font-bold focus:ring-2 focus:ring-purple-600">

            {{-- Resultados búsqueda --}}
            @if(!empty($associates))
                <div class="space-y-2">
                    @foreach($associates as $socio)
                        <button wire:click="seleccionarSocio({{ $socio->id }})"
                            class="w-full text-left p-3 bg-zinc-800/60 hover:bg-purple-600/20 rounded-xl border border-zinc-700 transition-all uppercase font-black text-white text-xs">
                            {{ $socio->last_name }}, {{ $socio->name }}
                            <span class="text-zinc-500 font-normal normal-case ml-1">— {{ $socio->dni }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Socio seleccionado --}}
            @if($asociadoSeleccionado)
                <div class="flex items-center justify-between bg-purple-900/20 border border-purple-500/30 rounded-2xl px-4 py-3">
                    <div>
                        <p class="text-purple-300 font-black text-sm uppercase">
                            {{ $asociadoSeleccionado->last_name }}, {{ $asociadoSeleccionado->name }}
                        </p>
                        <p class="text-zinc-500 text-[11px]">DNI: {{ $asociadoSeleccionado->dni }}</p>
                    </div>
                    <button wire:click="limpiarSocio" class="text-zinc-500 hover:text-white text-xs transition">✕ Cambiar</button>
                </div>

                {{-- Cuotas disponibles para seleccionar --}}
                @if($tiposCuota->where('active', true)->count())
                    <div class="space-y-2">
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Selecciona cuotas a cobrar</p>
                        @foreach($tiposCuota->where('active', true) as $tipo)
                            @php $yaPago = $tipo->ya_pago; @endphp
                            <label class="flex items-center gap-3 p-3 rounded-xl border transition-all cursor-pointer
                                {{ $yaPago ? 'border-green-800/30 bg-green-900/10 opacity-60 cursor-not-allowed' : 'border-zinc-700 bg-zinc-800/50 hover:bg-purple-600/10 hover:border-purple-500/30' }}">
                                <input type="checkbox"
                                    wire:click="toggleCuota({{ $tipo->id }})"
                                    {{ in_array($tipo->id, $cuotasSeleccionadas) ? 'checked' : '' }}
                                    {{ $yaPago ? 'disabled' : '' }}
                                    class="w-4 h-4 rounded border-zinc-600 bg-zinc-900 text-purple-600">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-black text-xs uppercase truncate">{{ $tipo->name }}</p>
                                    @if($tipo->description)
                                        <p class="text-zinc-500 text-[10px] truncate">{{ $tipo->description }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-white font-black text-sm">S/ {{ number_format($tipo->amount, 2) }}</p>
                                    @if($yaPago)
                                        <span class="text-green-500 text-[9px] font-bold uppercase">✓ Pagado</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-zinc-600 text-sm">
                        No hay cuotas extraordinarias activas. Créalas en el panel de la derecha.
                    </div>
                @endif

                {{-- Total y botón cobrar --}}
                @if(!empty($cuotasSeleccionadas))
                    <div class="bg-black/40 p-5 rounded-3xl border border-zinc-800 flex justify-between items-end">
                        <div>
                            <p class="text-zinc-500 font-bold text-[10px] uppercase">Total a Cobrar</p>
                            <p class="text-5xl text-green-500 font-black italic">S/ {{ number_format($totalFinal, 2) }}</p>
                        </div>
                        <button wire:click="confirmarPago"
                            class="bg-purple-600 hover:bg-purple-500 text-white font-black px-6 py-4 rounded-2xl uppercase text-xs transition">
                            Cobrar y Generar Recibo
                        </button>
                    </div>
                @endif

                {{-- Historial de pagos extraordinarios --}}
                @if(!empty($payments) && $payments->count())
                    <div class="pt-4 border-t border-zinc-800 space-y-3">
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Historial Extraordinarios</p>
                        @foreach($payments as $pago)
                            <div class="p-4 bg-zinc-800 rounded-2xl border border-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-0.5">
                                    <p class="text-zinc-400 text-[10px] uppercase tracking-widest">Recibo N° {{ $pago->invoice_number }}</p>
                                    <p class="text-white font-black text-xs">{{ strtoupper($pago->concept) }}</p>
                                    <p class="text-zinc-500 text-[11px]">{{ $pago->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-green-400 font-bold text-[11px]">S/ {{ number_format($pago->amount, 2) }}</p>
                                </div>
                                <button wire:click="imprimirPago({{ $pago->id }})"
                                    class="bg-green-600 hover:bg-green-500 text-white uppercase text-[11px] font-black px-4 py-2.5 rounded-2xl transition shrink-0">
                                    Imprimir
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                {{-- Estado vacío --}}
                <div class="py-10 text-center text-zinc-600 text-sm">
                    Busca y selecciona un socio para cobrarle.
                </div>
            @endif
        </div>

        {{-- ================================================================
             PANEL DERECHO — GESTIÓN DE TIPOS DE CUOTA
        ================================================================ --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-zinc-400 font-black text-[10px] uppercase tracking-widest">Tipos de Cuota Extraordinaria</h3>
                <button wire:click="abrirFormulario()"
                    class="bg-purple-600 hover:bg-purple-500 text-white font-black text-[10px] uppercase px-4 py-2 rounded-xl transition">
                    + Nueva Cuota
                </button>
            </div>

            {{-- Formulario crear/editar --}}
            @if($mostrarFormulario)
                <div class="bg-zinc-800 border border-purple-500/30 rounded-2xl p-5 space-y-4">
                    <p class="text-purple-300 font-black text-[10px] uppercase tracking-widest">
                        {{ $editandoId ? 'Editar Cuota' : 'Nueva Cuota Extraordinaria' }}
                    </p>

                    <div class="space-y-1">
                        <label class="text-zinc-500 text-[10px] uppercase font-bold">Nombre *</label>
                        <input type="text" wire:model="formNombre"
                            placeholder="Ej: Cuota Aniversario JASS"
                            class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-2.5 px-4 text-white text-sm focus:ring-2 focus:ring-purple-600">
                        @error('formNombre') <p class="text-red-400 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-zinc-500 text-[10px] uppercase font-bold">Descripción (opcional)</label>
                        <textarea wire:model="formDescripcion" rows="2"
                            placeholder="Acordado en reunión del..."
                            class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-2.5 px-4 text-white text-sm focus:ring-2 focus:ring-purple-600 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-zinc-500 text-[10px] uppercase font-bold">Monto (S/) *</label>
                            <input type="number" step="0.01" min="0.01" wire:model="formMonto"
                                placeholder="0.00"
                                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-2.5 px-4 text-white text-sm focus:ring-2 focus:ring-purple-600">
                            @error('formMonto') <p class="text-red-400 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-zinc-500 text-[10px] uppercase font-bold">Fecha de Reunión</label>
                            <input type="date" wire:model="formFecha"
                                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-2.5 px-4 text-white text-sm focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button wire:click="guardarTipo"
                            class="flex-1 bg-purple-600 hover:bg-purple-500 text-white font-black text-xs uppercase py-2.5 rounded-xl transition">
                            {{ $editandoId ? 'Actualizar' : 'Guardar' }}
                        </button>
                        <button wire:click="cerrarFormulario"
                            class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white font-black text-xs uppercase py-2.5 rounded-xl transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            @endif

            {{-- Lista de tipos de cuota --}}
            @if($tiposCuota->count())
                <div class="space-y-3">
                    @foreach($tiposCuota as $tipo)
                        <div class="p-4 bg-zinc-800/60 rounded-2xl border {{ $tipo->active ? 'border-zinc-700' : 'border-zinc-800 opacity-50' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-white font-black text-sm uppercase truncate">{{ $tipo->name }}</p>
                                        @if(!$tipo->active)
                                            <span class="text-zinc-500 text-[9px] font-bold uppercase border border-zinc-700 px-1.5 py-0.5 rounded">Inactiva</span>
                                        @endif
                                    </div>
                                    @if($tipo->description)
                                        <p class="text-zinc-500 text-[11px] mt-0.5">{{ $tipo->description }}</p>
                                    @endif
                                    @if($tipo->decided_at)
                                        <p class="text-zinc-600 text-[10px] mt-1">Reunión: {{ $tipo->decided_at->format('d/m/Y') }}</p>
                                    @endif
                                    <p class="text-purple-400 font-black text-base mt-1">S/ {{ number_format($tipo->amount, 2) }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <div class="flex gap-1.5">
                                        <button wire:click="abrirFormulario({{ $tipo->id }})"
                                            class="text-[10px] text-zinc-400 hover:text-white border border-zinc-700 hover:border-zinc-500 px-2 py-1 rounded-lg transition font-bold uppercase">
                                            Editar
                                        </button>
                                        <button wire:click="toggleActivo({{ $tipo->id }})"
                                            class="text-[10px] {{ $tipo->active ? 'text-amber-400 hover:text-amber-300 border-amber-800 hover:border-amber-600' : 'text-green-400 hover:text-green-300 border-green-800 hover:border-green-600' }} border px-2 py-1 rounded-lg transition font-bold uppercase">
                                            {{ $tipo->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </div>
                                    <button wire:click="eliminarTipo({{ $tipo->id }})"
                                        wire:confirm="¿Eliminar esta cuota? Se perderá el registro."
                                        class="text-[10px] text-red-500 hover:text-red-400 border border-red-900 hover:border-red-700 px-2 py-1 rounded-lg transition font-bold uppercase">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-zinc-600 text-sm">
                    Aún no hay cuotas extraordinarias. Crea la primera con el botón de arriba.
                </div>
            @endif
        </div>
    </div>
</div>
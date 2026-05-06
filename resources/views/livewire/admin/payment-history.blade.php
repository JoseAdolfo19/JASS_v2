<div class="max-w-6xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-black text-white uppercase italic">Historial de Pagos</h2>
        <input type="text" wire:model.live="search" placeholder="Buscar..." class="bg-zinc-900 border-zinc-800 rounded-xl text-white px-4 py-2 w-64">
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-zinc-800 text-zinc-500 text-[10px] font-black uppercase tracking-widest">
                <tr>
                    <th class="p-6">Recibo</th>
                    <th class="p-6">Socio</th>
                    <th class="p-6">Monto</th>
                    <th class="p-10">Concepto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach($payments as $pago)
                    <tr class="hover:bg-white/5 transition-all">
                        <td class="p-6 text-blue-500 font-black">#{{ $pago->invoice_number }}</td>
                        <!-- //nombre y apellido del socio -->
                        <td class="p-6 text-white font-bold">{{ $pago->associate->last_name }}, {{ $pago->associate->name }}</td>   
                        <td class="p-18 text-green-500 font-black">S/ {{ number_format($pago->amount, 2) }}</td>
                        <td class="p-10 text-zinc-500 text-[10px] uppercase font-bold">{{ $pago->concept }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6">{{ $payments->links() }}</div>
    </div>
</div>
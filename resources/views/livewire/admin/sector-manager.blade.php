<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-white">Gestión de Sectores</h2>

    @if (session()->has('message'))
        <div class="bg-green-900/30 border border-green-500 text-green-400 p-3 rounded-lg mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800 mb-8 shadow-lg">
        <form wire:submit.prevent="save" class="flex gap-4">
            <div class="flex-1">
                <input type="text" wire:model="name" placeholder="Nombre del sector (Ej: Sector Alto)"
                    class="w-full bg-zinc-800 border-zinc-700 rounded-lg p-2.5 text-white focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition">
                + Agregar
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($sectors as $sector)
            <div class="bg-zinc-900 p-4 rounded-lg border border-zinc-800 flex justify-between items-center group">
                <span class="text-lg text-zinc-100 font-medium">{{ $sector->name }}</span>
                <button wire:click="delete({{ $sector->id }})" 
                    class="text-zinc-600 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        @endforeach
    </div>
</div>
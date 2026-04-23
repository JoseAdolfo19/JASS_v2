<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JASS HUAYOCCARY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased font-sans">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-zinc-900 border-b border-zinc-800 p-4">
            <div class="container mx-auto flex justify-between items-center">
                <span class="text-xl font-bold text-blue-500">JASS HUAYOCCARY</span>
                <div class="space-x-4 flex items-center">
                    <a href="/home" class="hover:text-white">Inicio</a>
                    <a href="/sectores" class="hover:text-white">Sectores</a>
                    <a href="/asociados" class="hover:text-white">Asociados</a>

                    <div class="relative group inline-block">
                        <button type="button" class="text-zinc-200 hover:text-white transition px-2 py-1 rounded-sm border border-zinc-700 bg-zinc-900/80">
                            Caja
                        </button>
                        <div class="pointer-events-none opacity-0 invisible group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 absolute left-1/2 -translate-x-1/2 mt-2 w-72 rounded-xl border border-zinc-700 bg-zinc-950/95 shadow-2xl shadow-black/40 p-4 text-left z-20">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-sm text-sky-300 uppercase tracking-wide font-semibold mb-2">Ingresos</span>
                                    <ul class="space-y-2 text-sm">
                                        <li><a href="/pagos" class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">Pagos</a></li>
                                        <li><a href="/historial-pagos" class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">Historial de Pagos</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <span class="block text-sm text-rose-300 uppercase tracking-wide font-semibold mb-2">Egresos</span>
                                    <ul class="space-y-2 text-sm">
                                        <li><a href="{{ route('admin.egresos') }}" class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">Egresos</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.asistencia') }}" class="hover:text-white">Asistencia</a>
                    <a href="{{ route('admin.reportes') }}" class="hover:text-white">Reportes</a>
                    <a href="{{ route('admin.settings') }}" class="hover:text-white">Configuración</a>
                </div>
            </div>
        </nav>

        <main class="flex-grow container mx-auto p-6">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
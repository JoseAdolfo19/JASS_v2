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
                </span>
                <div class="space-x-4">
                    <a href="/home" class="hover:text-white">Inicio</a>
                    <a href="/sectores" class="hover:text-white">Sectores</a>
                    <a href="/asociados" class="hover:text-white">Asociados</a>
                    <a href="/pagos" class="hover:text-white">Pagos</a>
                    <a href="/historial-pagos" class="hover:text-white">Historial de Pagos</a>
                    <a href="{{ route('admin.egresos') }}" class="hover:text-white">Egresos</a>
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
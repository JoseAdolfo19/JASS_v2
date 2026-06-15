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

                <div class="space-x-4 flex items-center flex-wrap gap-y-2">
                    <a href="{{ route('admin.home') }}" class="hover:text-white transition">Inicio</a>
                    <a href="{{ route('admin.sectores') }}" class="hover:text-white transition">Sectores</a>
                    <a href="{{ route('admin.asociados') }}" class="hover:text-white transition">Asociados</a>

                    {{-- ── CAJA (dropdown) ── --}}
                    <div class="relative group inline-block">
                        <button type="button"
                            class="text-zinc-200 hover:text-white transition px-2 py-1 rounded-sm border border-zinc-700 bg-zinc-900/80">
                            Caja
                        </button>
                        <div class="pointer-events-none opacity-0 invisible
                                    group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto
                                    transition-all duration-200
                                    absolute left-1/2 -translate-x-1/2 mt-2 w-80
                                    rounded-xl border border-zinc-700 bg-zinc-950/95
                                    shadow-2xl shadow-black/40 p-4 text-left z-20">
                            <div class="grid grid-cols-2 gap-4">

                                {{-- Ingresos --}}
                                <div>
                                    <span class="block text-sm text-sky-300 uppercase tracking-wide font-semibold mb-2">
                                        Ingresos
                                    </span>
                                    <ul class="space-y-2 text-sm">
                                        <li>
                                            <a href="{{ route('admin.pagos') }}"
                                               class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">
                                                Cuota Familiar
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.otros') }}"
                                               class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">
                                                Cuotas Extraordinarias
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Egresos / Multas --}}
                                <div>
                                    <span class="block text-sm text-rose-300 uppercase tracking-wide font-semibold mb-2">
                                        Egresos
                                    </span>
                                    <ul class="space-y-2 text-sm">
                                        <li>
                                            <a href="{{ route('admin.egresos') }}"
                                               class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">
                                                Egresos
                                            </a>
                                        </li>
                                    </ul>

                                    <span class="block text-sm text-amber-300 uppercase tracking-wide font-semibold mt-4 mb-2">
                                        Multas
                                    </span>
                                    <ul class="space-y-2 text-sm">
                                        <li>
                                            <a href="{{ route('admin.multas') }}"
                                               class="block rounded-md px-2 py-1 hover:bg-zinc-800 hover:text-white">
                                                Multas y Faltas
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- ── FIN CAJA ── --}}

                    <a href="{{ route('admin.historial-pagos') }}" class="hover:text-white transition">Historial</a>
                    <a href="{{ route('admin.asistencia') }}" class="hover:text-white transition">Asistencia</a>
                    <a href="{{ route('admin.reportes') }}" class="hover:text-white transition">Reportes</a>
                    <a href="{{ route('admin.settings') }}" class="hover:text-white transition">Configuración</a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-zinc-200 hover:text-white transition px-2 py-1 rounded-sm border border-zinc-700 bg-zinc-900/80">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="flex-grow container mx-auto p-6">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts

    <script>
    // Descarga el PDF y redirige al home después
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('descargar-y-redirigir', ({ url, redirectTo }) => {
            // Crear enlace invisible y hacer clic para descargar
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            // Redirigir tras un breve delay para que el download inicie
            setTimeout(() => {
                window.location.href = redirectTo;
            }, 1200);
        });

        // Solo descarga, sin redirigir (para reimprimir pagos anteriores)
        Livewire.on('descargar-pdf', ({ url }) => {
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso | J.A.S.S. Gestión</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-black text-white font-['Instrument_Sans'] selection:bg-blue-500/30">
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden">
        
        {{-- Fondo con efecto de luz decorativa --}}
        <div class="absolute top-0 -left-4 w-72 h-72 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute bottom-0 -right-4 w-72 h-72 bg-red-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>

        <div class="w-full max-w-[440px] z-10">
            {{-- Logo y Título --}}
            <div class="text-center mb-10">
                <div class="inline-flex p-4 bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl mb-6">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.283a2 2 0 01-1.198.043l-2.431-.54a2 2 0 01-1.064-3.51l.799-.549a2 2 0 011.215-.45l2.56-.005a2 2 0 001.596-.608l1.183-1.183a2 2 0 011.616-.593l2.452.174a2 2 0 011.025 3.511l-.967.697a2 2 0 00-.763 1.58l-.033 2.112z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-black italic tracking-tighter uppercase">J.A.S.S. <span class="text-blue-600">SISTEMA</span></h1>
                <p class="text-zinc-500 font-bold text-[10px] uppercase tracking-[0.3em] mt-2">Plataforma de Control Administrativo</p>
            </div>

            {{-- Card de Login --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-10 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)]">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-500 text-xs font-bold uppercase tracking-tight">
                        @foreach ($errors->all() as $error)
                            <p>× {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-2 px-1">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-zinc-800 border-zinc-700 rounded-2xl px-5 py-4 text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none transition-all placeholder-zinc-600 border"
                            placeholder="admin@jass.com">
                    </div>

                    <div>
                        <label class="block text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-2 px-1">Contraseña</label>
                        <input type="password" name="password" required
                            class="w-full bg-zinc-800 border-zinc-700 rounded-2xl px-5 py-4 text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none transition-all placeholder-zinc-600 border"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-zinc-700 bg-zinc-800 text-blue-600 focus:ring-blue-600 focus:ring-offset-zinc-900">
                            <span class="text-[10px] font-bold text-zinc-500 group-hover:text-zinc-300 uppercase">Recordarme</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-blue-900/20 uppercase text-xs tracking-[0.2em] active:scale-95">
                        Iniciar Sesión
                    </button>
                </form>
            </div>

            <p class="mt-8 text-center text-zinc-600 text-[9px] font-black uppercase tracking-widest">
                &copy; {{ date('Y') }} J.A.S.S. Gestión Hídrica Comunal
            </p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · @yield('title', 'Panel') — ServiPueblo</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛠️</text></svg>">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Loading overlay --}}
    <div id="loading-overlay"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 rounded-full border-4 border-brand-200 opacity-30"></div>
                <div class="absolute inset-0 rounded-full border-4 border-t-brand-500 border-r-transparent border-b-transparent border-l-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center text-2xl">🛠️</div>
            </div>
            <span class="text-white text-sm font-medium tracking-wide">Cargando...</span>
        </div>
    </div>

    {{-- Top bar móvil --}}
    <header class="bg-gray-900 text-white flex items-center justify-between px-4 py-3 md:hidden sticky top-0 z-40">
        <span class="font-bold">🛠️ Admin</span>
        <button onclick="toggleMenu()" class="text-gray-300 text-2xl leading-none">☰</button>
    </header>

    {{-- Menú móvil overlay --}}
    <div id="mobile-menu" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleMenu()"></div>
        <nav class="absolute left-0 top-0 bottom-0 w-64 bg-gray-900 text-gray-300 flex flex-col p-4">
            <button onclick="toggleMenu()" class="text-gray-400 text-xl self-end mb-4">✕</button>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">📊 Dashboard</a>
            <a href="{{ route('admin.workers.index') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">👷 Trabajadores</a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">🗂️ Categorías</a>
            <a href="{{ route('admin.ratings.index') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">⭐ Calificaciones</a>
            <a href="{{ route('admin.metrics.index') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">📈 Métricas</a>
            <a href="{{ route('admin.chat-logs.index') }}" class="flex items-center gap-2 py-3 border-b border-gray-700">💬 Chat IA</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-red-400">Cerrar sesión</button>
            </form>
        </nav>
    </div>

    <div class="flex min-h-screen">

        {{-- Sidebar desktop --}}
        <aside class="hidden md:flex w-52 bg-gray-900 text-gray-300 flex-col shrink-0">
            <div class="px-4 py-5 text-white font-bold text-lg border-b border-gray-700">
                🛠️ ServiPueblo
                <div class="text-xs font-normal text-gray-400">Panel Admin</div>
            </div>
            <nav class="flex-1 py-4 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.workers.index') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.workers.*') ? 'bg-gray-700 text-white' : '' }}">
                    👷 Trabajadores
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700 text-white' : '' }}">
                    🗂️ Categorías
                </a>
                <a href="{{ route('admin.ratings.index') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.ratings.*') ? 'bg-gray-700 text-white' : '' }}">
                    ⭐ Calificaciones
                </a>
                <a href="{{ route('admin.metrics.index') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.metrics.*') ? 'bg-gray-700 text-white' : '' }}">
                    📈 Métricas
                </a>
                <a href="{{ route('admin.chat-logs.index') }}"
                   class="flex items-center gap-2 px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.chat-logs.*') ? 'bg-gray-700 text-white' : '' }}">
                    💬 Chat IA
                </a>
            </nav>
            <div class="px-4 py-3 border-t border-gray-700 text-xs">
                <span class="block text-gray-400 mb-1">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300">Cerrar sesión</button>
                </form>
                <a href="mailto:servipueblosoporte@gmail.com" class="block text-gray-500 hover:text-gray-300 mt-2">✉️ Soporte</a>
            </div>
        </aside>

        {{-- Contenido --}}
        <div class="flex-1 flex flex-col overflow-x-auto">
            <header class="hidden md:block bg-white shadow-sm px-6 py-4">
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </header>

            <div class="px-4 md:px-6 mt-4">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 text-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-300 text-red-800 rounded px-4 py-3 text-sm mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-700 rounded px-4 py-3 text-sm mb-4">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <main class="flex-1 px-4 md:px-6 py-4">
                @yield('content')
            </main>
        </div>

    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    <script>
        function toggleMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }
    </script>
    @stack('scripts')

    <script>
    (function () {
        var overlay = document.getElementById('loading-overlay');

        function hideLoader() {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
        }

        function showLoader() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'all';
        }

        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 3000);

        document.addEventListener('submit', function () { showLoader(); });

        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript')
                || href.startsWith('mailto') || href.startsWith('tel')
                || href.startsWith('http') || href.startsWith('//')) return;
            showLoader();
        });
    })();
    </script>
</body>
</html>

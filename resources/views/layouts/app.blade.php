<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ServiPueblo') — Encuentra trabajadores en tu pueblo</title>
    <meta name="description" content="@yield('description', 'Directorio de trabajadores y servicios en pueblos pequeños.')">

    {{-- Open Graph base (se sobreescribe por página) --}}
    <meta property="og:site_name" content="ServiPueblo">
    <meta property="og:locale" content="es_AR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'ServiPueblo — Encuentra trabajadores en tu pueblo')">
    <meta property="og:description" content="@yield('og_description', 'Directorio de trabajadores y servicios en pueblos pequeños. Plomeros, electricistas, carpinteros y más.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:image:width" content="@yield('og_image_width', '1200')">
    <meta property="og:image:height" content="@yield('og_image_height', '630')">

    {{-- Twitter / X Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'ServiPueblo')">
    <meta name="twitter:description" content="@yield('og_description', 'Directorio de trabajadores en pueblos pequeños.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

    @stack('meta')
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛠️</text></svg>">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ServiPueblo">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    {{-- Loading overlay --}}
    <div id="loading-overlay"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4">
            {{-- Spinner doble aro --}}
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 rounded-full border-4 border-brand-200 opacity-30"></div>
                <div class="absolute inset-0 rounded-full border-4 border-t-brand-500 border-r-transparent border-b-transparent border-l-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center text-2xl">🛠️</div>
            </div>
            <span class="text-white text-sm font-medium tracking-wide">Cargando...</span>
        </div>
    </div>

    {{-- Navbar --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-bold text-brand-600">
                🛠️ ServiPueblo
            </a>
            <nav class="flex gap-4 text-sm text-gray-600 items-center">
                <a href="{{ route('workers.index') }}" class="hover:text-brand-600">Trabajadores</a>
                <a href="{{ route('workers.apply') }}" class="hover:text-brand-600">Registrate</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600">Admin</a>
                    @else
                        <a href="{{ route('worker.profile.edit') }}" class="hover:text-brand-600">Mi perfil</a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="text-brand-600 border border-brand-300 hover:bg-brand-50 rounded-full px-3 py-1 transition-colors">
                        Ingresar
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto px-4 mt-4 w-full">
            <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-5xl mx-auto px-4 mt-4 w-full">
            <div class="bg-red-100 border border-red-300 text-red-800 rounded px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Contenido principal --}}
    <main class="flex-1 max-w-5xl mx-auto px-4 py-6 w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-auto py-4 text-center text-xs text-gray-400">
        © {{ date('Y') }} ServiPueblo · Conectando pueblos con trabajadores
        <span class="mx-2">·</span>
        Soporte: <a href="mailto:servipueblosoporte@gmail.com" class="hover:text-brand-600">servipueblosoporte@gmail.com</a>
        <span class="mx-2">·</span>
        @guest
            <a href="{{ route('login') }}" class="hover:text-gray-600">Acceso admin</a>
        @else
            <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600">Admin</a>
            · <form method="POST" action="{{ route('logout') }}" class="inline"><button type="submit" class="hover:text-red-500">Salir</button>@csrf</form>
        @endguest
    </footer>

    <script src="{{ mix('js/app.js') }}"></script>
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

        // Ocultar cuando la página termina de cargar
        window.addEventListener('load', hideLoader);

        // Fallback: ocultar si después de 8s sigue visible
        setTimeout(hideLoader, 8000);
        // Ocultar también si el usuario vuelve a la pestaña (visibilitychange)
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) hideLoader();
        });

        // Mostrar en submit de formularios (excepto chat y búsquedas con method GET rápidas)
        document.addEventListener('submit', function (e) {
            var form = e.target;
            // No bloquear el form del chat widget
            if (form.id === 'chat-form') return;
            showLoader();
        });

        // Mostrar en clicks de navegación (links internos, misma pestaña)
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            // No mostrar si abre en nueva pestaña
            if (link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.which === 2) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript')
                || href.startsWith('mailto') || href.startsWith('tel')) return;
            // Permitir URLs absolutas del mismo dominio, ignorar externas
            if (href.startsWith('http') || href.startsWith('//')) {
                try {
                    if (new URL(href).hostname !== window.location.hostname) return;
                } catch(e) { return; }
            }
            showLoader();
        });
    })();
    </script>

    {{-- Widget de IA --}}
    <div id="chat-widget" class="fixed bottom-5 right-4 z-50 flex flex-col items-end gap-3">

        {{-- Ventana de chat --}}
        <div id="chat-box"
             class="hidden w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden"
             style="max-height: 420px">

            {{-- Header --}}
            <div class="bg-brand-600 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></div>
                    <span class="text-white font-semibold text-sm">Asistente ServiPueblo</span>
                </div>
                <button onclick="toggleChat()" class="text-white opacity-70 hover:opacity-100 text-lg leading-none">✕</button>
            </div>

            {{-- Mensajes --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-sm" style="min-height:180px">
                <div class="flex gap-2">
                    <div class="w-6 h-6 rounded-full bg-brand-100 flex items-center justify-center shrink-0 text-xs">🤖</div>
                    <div class="bg-gray-100 rounded-2xl rounded-tl-none px-3 py-2 text-gray-700 max-w-xs">
                        ¡Hola! ¿Qué servicio necesitás? Te ayudo a encontrar el trabajador ideal en tu pueblo.
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="border-t border-gray-100 px-3 py-2 flex gap-2">
                <input type="text" id="chat-input"
                       placeholder="Ej: necesito un plomero..."
                       maxlength="200"
                       class="flex-1 text-sm border border-gray-200 rounded-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-400"
                       onkeydown="if(event.key==='Enter') sendMessage()">
                <button onclick="sendMessage()"
                        class="bg-brand-600 hover:bg-brand-700 text-white rounded-full w-9 h-9 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Botón flotante --}}
        <button onclick="toggleChat()"
                class="bg-brand-600 hover:bg-brand-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all hover:scale-105">
            <span id="chat-icon" class="text-2xl">🤖</span>
        </button>
    </div>

    <script>
    var chatOpen = false;
    var csrfToken = '{{ csrf_token() }}';
    var chatUrl = '{{ route('ai.chat') }}';
    var chatHistory = [];

    function toggleChat() {
        chatOpen = !chatOpen;
        document.getElementById('chat-box').classList.toggle('hidden', !chatOpen);
        document.getElementById('chat-icon').textContent = chatOpen ? '✕' : '🤖';
        if (chatOpen) document.getElementById('chat-input').focus();
    }

    function addMessage(text, isUser) {
        var messages = document.getElementById('chat-messages');
        var div = document.createElement('div');
        div.className = 'flex gap-2' + (isUser ? ' justify-end' : '');

        if (isUser) {
            div.innerHTML = '<div class="bg-brand-600 text-white rounded-2xl rounded-tr-none px-3 py-2 max-w-xs text-sm">' + escapeHtml(text) + '</div>';
        } else {
            var formatted = escapeHtml(text)
                // links markdown [texto](url)
                .replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/g, '<a href="$2" class="underline text-brand-600" target="_blank">$1</a>')
                // saltos de línea
                .replace(/\n/g, '<br>')
                // bullets • al inicio de línea
                .replace(/(^|<br>)•\s*/g, '$1<span class="text-brand-600 font-bold">•</span> ');
            div.innerHTML = '<div class="w-6 h-6 rounded-full bg-brand-100 flex items-center justify-center shrink-0 text-xs">🤖</div><div class="bg-gray-100 rounded-2xl rounded-tl-none px-3 py-2 text-gray-700 max-w-xs text-sm leading-relaxed">' + formatted + '</div>';
        }

        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    function escapeHtml(text) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(text));
        return d.innerHTML;
    }

    function addTyping() {
        var messages = document.getElementById('chat-messages');
        var div = document.createElement('div');
        div.id = 'typing-indicator';
        div.className = 'flex gap-2';
        div.innerHTML = '<div class="w-6 h-6 rounded-full bg-brand-100 flex items-center justify-center shrink-0 text-xs">🤖</div><div class="bg-gray-100 rounded-2xl rounded-tl-none px-3 py-2 text-gray-500 text-sm">...</div>';
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    function sendMessage() {
        var input = document.getElementById('chat-input');
        var text = input.value.trim();
        if (!text) return;

        addMessage(text, true);
        input.value = '';
        addTyping();

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message: text, history: chatHistory.slice(-6) })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var typing = document.getElementById('typing-indicator');
            if (typing) typing.remove();
            var reply = data.reply || 'No pude responder. Intentá de nuevo.';
            // Guardar en historial
            chatHistory.push({ role: 'user', content: text });
            chatHistory.push({ role: 'assistant', content: reply });
            if (chatHistory.length > 12) chatHistory = chatHistory.slice(-12);
            addMessage(reply, false);
        })
        .catch(function() {
            var typing = document.getElementById('typing-indicator');
            if (typing) typing.remove();
            addMessage('Hubo un error. Intentá de nuevo.', false);
        });
    }
    </script>

    {{-- PWA: banner instalación + service worker --}}
    <div id="pwa-banner"
         class="hidden fixed bottom-0 left-0 right-0 z-[9998] bg-white border-t border-gray-200 shadow-lg px-4 py-3 flex items-center gap-3">
        <img src="/icons/icon-192.png" class="w-10 h-10 rounded-xl shrink-0" alt="ServiPueblo">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">Instalá ServiPueblo</p>
            <p class="text-xs text-gray-500">Accedé rápido desde tu pantalla de inicio</p>
        </div>
        <button id="pwa-install-btn"
                class="shrink-0 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Instalar
        </button>
        <button id="pwa-dismiss-btn" class="shrink-0 text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
    </div>

    <script>
    // Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    // Banner de instalación (Android/Chrome)
    (function () {
        var deferredPrompt = null;
        var banner = document.getElementById('pwa-banner');
        var installBtn = document.getElementById('pwa-install-btn');
        var dismissBtn = document.getElementById('pwa-dismiss-btn');

        // No mostrar si ya fue descartado o instalado
        if (localStorage.getItem('pwa-dismissed')) return;

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            // Mostrar banner con pequeño delay para no interrumpir la carga
            setTimeout(function () {
                banner.classList.remove('hidden');
                banner.classList.add('flex');
            }, 3000);
        });

        installBtn.addEventListener('click', function () {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (result) {
                deferredPrompt = null;
                banner.classList.add('hidden');
                if (result.outcome === 'accepted') {
                    localStorage.setItem('pwa-dismissed', '1');
                }
            });
        });

        dismissBtn.addEventListener('click', function () {
            banner.classList.add('hidden');
            localStorage.setItem('pwa-dismissed', '1');
        });

        // Si ya está instalada como PWA, ocultar banner
        if (window.matchMedia('(display-mode: standalone)').matches) {
            localStorage.setItem('pwa-dismissed', '1');
        }
    })();
    </script>
</body>
</html>

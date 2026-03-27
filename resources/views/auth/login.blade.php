<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar · ServiPueblo</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-sm">
    <div class="text-center mb-6">
        <div class="text-3xl font-bold text-gray-900">🛠️ ServiPueblo</div>
        <p class="text-sm text-gray-500 mt-1">Acceso para trabajadores y administradores</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-lg font-semibold text-gray-800 mb-6">Iniciar sesión</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-2.5 rounded transition-colors">
                Entrar
            </button>
        </form>
    </div>

    <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-xs text-blue-700 text-center">
        ¿Sos trabajador o emprendedor y querés gestionar tu perfil?<br>
        Escribinos a <a href="mailto:servipueblosoporte@gmail.com" class="font-medium underline hover:text-blue-900">servipueblosoporte@gmail.com</a>
        y te creamos tu acceso.
    </div>

    <p class="text-center mt-3 text-xs text-gray-400">
        <a href="{{ route('home') }}" class="hover:underline">← Ver sitio público</a>
    </p>
</div>

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin conexión — ServiPueblo</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-sm">
        <div class="text-6xl mb-4">📡</div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">Sin conexión</h1>
        <p class="text-gray-500 text-sm mb-6">
            No hay internet en este momento. Revisá tu conexión e intentá de nuevo.
        </p>
        <button onclick="window.location.reload()"
                class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
            Reintentar
        </button>
        <div class="mt-6 text-xs text-gray-400">ServiPueblo</div>
    </div>
</body>
</html>

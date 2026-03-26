@extends('layouts.app')

@section('title', 'Error del servidor')

@section('content')
<div class="max-w-md mx-auto text-center py-16">
    <div class="text-6xl mb-4">⚙️</div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Algo salió mal</h1>
    <p class="text-gray-500 mb-8">Hubo un error interno. Ya estamos al tanto. Intentá de nuevo en unos minutos.</p>
    <a href="{{ route('home') }}"
       class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
        Volver al inicio
    </a>
</div>
@endsection

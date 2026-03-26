@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="max-w-md mx-auto text-center py-16">
    <div class="text-6xl mb-4">🔍</div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Página no encontrada</h1>
    <p class="text-gray-500 mb-8">El trabajador o la página que buscás no existe o fue removida.</p>
    <a href="{{ route('home') }}"
       class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
        Ver trabajadores disponibles
    </a>
</div>
@endsection

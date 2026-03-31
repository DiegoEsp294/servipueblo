@extends('layouts.app')

@section('title', $tipo === 'entrepreneur' ? 'Emprendimientos locales' : ($tipo === 'worker' ? 'Trabajadores y oficios' : 'Directorio local'))

@section('content')

{{-- Hero --}}
<div class="text-center mb-6">
    @if($tipo === 'entrepreneur')
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Emprendimientos de tu pueblo</h1>
        <p class="text-gray-500">Tiendas, productos y servicios locales · contacto directo por WhatsApp</p>
    @elseif($tipo === 'worker')
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Trabajadores y oficios en tu pueblo</h1>
        <p class="text-gray-500">Plomeros, electricistas, carpinteros y más · contacto directo por WhatsApp</p>
    @else
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Encontrá lo que necesitás en tu pueblo</h1>
        <p class="text-gray-500">Trabajadores, oficios y emprendimientos locales · contacto directo por WhatsApp</p>
    @endif
</div>

{{-- Tabs Todos / Oficios / Emprendimientos --}}
@php
    $baseParams = request()->except(['tipo', 'page']);
    $tabUrl = fn($t) => route('home', $t ? array_merge($baseParams, ['tipo' => $t]) : $baseParams);
@endphp
<div class="flex rounded-xl border border-gray-200 p-1 bg-gray-50 mb-6 w-full sm:w-fit mx-auto">
    @foreach(['' => '🗂️ Todos', 'worker' => '🔧 Oficios', 'entrepreneur' => '🏪 Emprendimientos'] as $val => $label)
        <a href="{{ $tabUrl($val) }}"
           class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-sm font-medium transition-all
                  {{ ($tipo ?? '') === $val ? 'bg-white shadow text-brand-600' : 'text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('workers.index') }}"
      class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">

    @if($tipo)
        <input type="hidden" name="tipo" value="{{ $tipo }}">
    @endif

    <div class="w-full sm:flex-1">
        <label class="block text-xs font-medium text-gray-600 mb-1">🔍 Nombre</label>
        <input type="text" name="nombre" value="{{ request('nombre') }}"
               placeholder="{{ $tipo === 'entrepreneur' ? 'Ej: La Alacena' : 'Ej: Juan García' }}"
               autocomplete="off"
               class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
    </div>

    <div class="w-full sm:flex-1">
        <label class="block text-xs font-medium text-gray-600 mb-1">
            📍 Pueblo
            @if($pueblo)
                <span class="text-brand-600">(recordado)</span>
            @endif
        </label>
        <input type="text" name="pueblo" value="{{ $pueblo }}"
               list="towns-list"
               placeholder="Ej: San Marcos"
               autocomplete="off"
               class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
        <datalist id="towns-list">
            @foreach($towns as $town)
                <option value="{{ $town }}">
            @endforeach
        </datalist>
    </div>

    <div class="w-full sm:flex-1">
        <label class="block text-xs font-medium text-gray-600 mb-1">Categoría</label>
        <select name="categoria" class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">Todas las categorías</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('categoria') === $cat->slug ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }} ({{ $cat->workers_count }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <button type="submit"
                class="flex-1 sm:flex-none bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2.5 rounded transition-colors">
            Buscar
        </button>
        @if($pueblo || request('categoria') || request('nombre'))
            <a href="{{ route('workers.index', array_filter(['tipo' => $tipo, 'limpiar' => 1])) }}"
               class="flex-1 sm:flex-none text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded transition-colors">
                Limpiar
            </a>
        @endif
    </div>
</form>

{{-- Banner pueblo activo --}}
@if($pueblo && !request()->has('pueblo') && !request()->has('categoria'))
    <div class="bg-brand-50 border border-brand-200 rounded-lg px-4 py-3 mb-4 flex items-center justify-between text-sm">
        <span class="text-brand-700">📍 Mostrando resultados de <strong>{{ $pueblo }}</strong> (guardado)</span>
        <a href="{{ route('workers.index', array_filter(['tipo' => $tipo, 'limpiar' => 1])) }}" class="text-brand-500 hover:text-brand-700 font-medium ml-3 whitespace-nowrap">Limpiar</a>
    </div>
@endif

{{-- Categorías rápidas --}}
@if($categories->isNotEmpty())
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($categories as $cat)
        <a href="{{ route('category.landing', $cat->slug) }}"
           class="inline-flex items-center gap-1 text-sm px-3 py-1.5 rounded-full border
                  {{ request('categoria') === $cat->slug
                     ? 'bg-brand-600 text-white border-brand-600'
                     : 'bg-white text-gray-600 border-gray-200 hover:border-brand-400' }}
                  transition-colors">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
    @endforeach
</div>
@endif

{{-- Resultados --}}
@php
    $total = $workers->total();
    if ($tipo === 'worker') {
        $label = $total === 1 ? 'trabajador encontrado' : 'trabajadores encontrados';
    } elseif ($tipo === 'entrepreneur') {
        $label = $total === 1 ? 'emprendimiento encontrado' : 'emprendimientos encontrados';
    } else {
        $label = $total === 1 ? 'resultado encontrado' : 'resultados encontrados';
    }
@endphp
<div class="mb-3 text-sm text-gray-500">
    {{ $total }} {{ $label }}
</div>

@if($workers->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-4xl mb-3">🔍</div>
        <p class="font-medium">
            @if($tipo === 'entrepreneur')
                No encontramos emprendimientos con esos filtros.
            @elseif($tipo === 'worker')
                No encontramos trabajadores con esos filtros.
            @else
                No encontramos resultados con esos filtros.
            @endif
        </p>
        <a href="{{ route('workers.index', array_filter(['tipo' => $tipo])) }}" class="text-brand-600 hover:underline text-sm mt-1 inline-block">Ver todos</a>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($workers as $worker)
            <x-worker-card :worker="$worker" :show-type="!$tipo" />
        @endforeach
    </div>

    <div class="mt-6">
        {{ $workers->links() }}
    </div>
@endif

@endsection

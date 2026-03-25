@extends('layouts.app')

@section('title', 'Directorio de trabajadores')

@section('content')

{{-- Hero --}}
<div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Encuentra trabajadores en tu pueblo</h1>
    <p class="text-gray-500">Plomeros, electricistas, carpinteros y más · contacto directo por WhatsApp</p>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('workers.index') }}"
      class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">

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
        @if($pueblo || request('categoria'))
            <a href="{{ route('workers.index', ['limpiar' => 1]) }}"
               class="flex-1 sm:flex-none text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded transition-colors">
                Limpiar
            </a>
        @endif
    </div>
</form>

{{-- Banner pueblo activo --}}
@if($pueblo && !request()->has('pueblo') && !request()->has('categoria'))
    <div class="bg-brand-50 border border-brand-200 rounded-lg px-4 py-3 mb-4 flex items-center justify-between text-sm">
        <span class="text-brand-700">📍 Mostrando trabajadores de <strong>{{ $pueblo }}</strong> (guardado)</span>
        <a href="{{ route('workers.index', ['limpiar' => 1]) }}" class="text-brand-500 hover:text-brand-700 font-medium ml-3 whitespace-nowrap">Limpiar</a>
    </div>
@endif

{{-- Categorías rápidas --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($categories as $cat)
        <a href="{{ route('categories.show', $cat->slug) }}"
           class="inline-flex items-center gap-1 text-sm px-3 py-1.5 rounded-full border
                  {{ request('categoria') === $cat->slug
                     ? 'bg-brand-600 text-white border-brand-600'
                     : 'bg-white text-gray-600 border-gray-200 hover:border-brand-400' }}
                  transition-colors">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
    @endforeach
</div>

{{-- Resultados --}}
<div class="mb-3 text-sm text-gray-500">
    {{ $workers->total() }} {{ $workers->total() === 1 ? 'trabajador encontrado' : 'trabajadores encontrados' }}
</div>

@if($workers->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-4xl mb-3">🔍</div>
        <p class="font-medium">No encontramos trabajadores con esos filtros.</p>
        <a href="{{ route('workers.index') }}" class="text-brand-600 hover:underline text-sm mt-1 inline-block">Ver todos</a>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($workers as $worker)
            <x-worker-card :worker="$worker" />
        @endforeach
    </div>

    <div class="mt-6">
        {{ $workers->links() }}
    </div>
@endif

@endsection

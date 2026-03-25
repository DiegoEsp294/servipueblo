@extends('layouts.app')

@section('title', $category->name)

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">
        {{ $category->icon ?? '' }} {{ $category->name }}
    </h1>
    <p class="text-gray-500 text-sm mt-1">{{ $workers->total() }} trabajadores disponibles</p>
</div>

{{-- Filtro por categorías --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('workers.index') }}"
       class="text-sm px-3 py-1.5 rounded-full border bg-white text-gray-600 border-gray-200 hover:border-brand-400 transition-colors">
        Todas
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('categories.show', $cat->slug) }}"
           class="inline-flex items-center gap-1 text-sm px-3 py-1.5 rounded-full border
                  {{ $cat->id === $category->id
                     ? 'bg-brand-600 text-white border-brand-600'
                     : 'bg-white text-gray-600 border-gray-200 hover:border-brand-400' }}
                  transition-colors">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
    @endforeach
</div>

@if($workers->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-4xl mb-3">🔍</div>
        <p>No hay trabajadores en esta categoría todavía.</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($workers as $worker)
            <x-worker-card :worker="$worker" />
        @endforeach
    </div>
    <div class="mt-6">{{ $workers->links() }}</div>
@endif

@endsection

@extends('layouts.admin')
@section('title', 'Categorías')

@section('content')

<div class="grid gap-6 md:grid-cols-2">

    {{-- Lista de categorías --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">Categoría</th>
                    <th class="text-left px-4 py-3">Trabajadores</th>
                    <th class="text-left px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-gray-50 last:border-0">
                    <td class="px-4 py-3">
                        <span class="font-medium">{{ $category->icon }} {{ $category->name }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $category->workers_count }}</td>
                    <td class="px-4 py-3">
                        @if($category->workers_count === 0)
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('¿Eliminar categoría {{ addslashes($category->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Eliminar</button>
                            </form>
                        @else
                            <span class="text-gray-300 text-xs">Tiene trabajadores</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No hay categorías.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Agregar categoría --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Agregar categoría</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ej: Gasero"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ícono (emoji, opcional)</label>
                <input type="text" name="icon" value="{{ old('icon') }}"
                       placeholder="🔧"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <button type="submit"
                    class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                Agregar categoría
            </button>
        </form>
    </div>

</div>

@endsection

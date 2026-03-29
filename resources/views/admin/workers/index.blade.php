@extends('layouts.admin')
@section('title', 'Trabajadores')

@section('content')

@php $pendingCount = \App\Models\Worker::where('is_active', false)->count(); @endphp

@if($pendingCount)
<div class="bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-3 mb-4 flex items-center justify-between text-sm">
    <span class="text-yellow-800 font-medium">⏳ {{ $pendingCount }} solicitud(es) pendiente(s) de aprobación</span>
    <a href="?estado=pendiente" class="text-yellow-700 underline">Ver solo pendientes</a>
</div>
@endif

@if($sinCuenta)
<div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-4 flex items-center justify-between text-sm">
    <span class="text-blue-800 font-medium">🔑 {{ $sinCuenta }} trabajador(es) sin cuenta de acceso</span>
    <a href="?cuenta=sin" class="text-blue-700 underline">Ver sin cuenta</a>
</div>
@endif

{{-- Buscador y filtros --}}
<form method="GET" class="bg-white rounded-lg border border-gray-100 shadow-sm p-3 mb-4 flex flex-wrap gap-2 items-end">
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="busqueda" value="{{ request('busqueda') }}"
               placeholder="Nombre, descripción, pueblo..."
               class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div class="min-w-[140px]">
        <label class="block text-xs text-gray-500 mb-1">Categoría</label>
        <select name="categoria" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white">
            <option value="">Todas</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->emoji }} {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[130px]">
        <label class="block text-xs text-gray-500 mb-1">Estado</label>
        <select name="estado" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white">
            <option value="">Todos</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
        </select>
    </div>
    <div class="min-w-[140px]">
        <label class="block text-xs text-gray-500 mb-1">Cuenta de acceso</label>
        <select name="cuenta" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white">
            <option value="">Todas</option>
            <option value="sin" {{ request('cuenta') === 'sin' ? 'selected' : '' }}>Sin cuenta</option>
            <option value="con" {{ request('cuenta') === 'con' ? 'selected' : '' }}>Con cuenta</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['busqueda','categoria','estado','cuenta']))
            <a href="{{ route('admin.workers.index') }}"
               class="text-sm text-gray-500 px-3 py-2 hover:underline">
                Limpiar
            </a>
        @endif
    </div>
    <div class="ml-auto">
        <a href="{{ route('admin.workers.create') }}"
           class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors inline-block">
            + Agregar trabajador
        </a>
    </div>
</form>

<p class="text-xs text-gray-400 mb-3">{{ $workers->total() }} resultado(s)</p>

{{-- Mobile: tarjetas --}}
<div class="sm:hidden space-y-3">
    @forelse($workers as $worker)
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-gray-900">{{ $worker->name }}</p>
                    @if(!$worker->photo_path)
                        <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5">Sin foto</span>
                    @endif
                    @if($worker->user)
                        <span class="text-xs text-green-700 bg-green-50 border border-green-200 rounded px-1.5 py-0.5">🔑 Con cuenta</span>
                    @else
                        <span class="text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded px-1.5 py-0.5">Sin cuenta</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">{{ $worker->categories->pluck('name')->join(', ') }} · {{ $worker->town }}</p>
                @if($worker->user)
                    <p class="text-xs text-gray-400 mt-0.5">✉ {{ $worker->user->email }}</p>
                @endif
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium shrink-0
                {{ $worker->is_active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $worker->is_active ? 'Activo' : 'Pendiente' }}
            </span>
        </div>
        <p class="text-xs text-gray-400 mb-3">👍 {{ $worker->recommendations_count }}/{{ $worker->ratings_count }} recomendaciones</p>
        <div class="flex flex-wrap gap-2 text-sm border-t border-gray-50 pt-3">
            @if(!$worker->is_active)
                <form method="POST" action="{{ route('admin.workers.approve', $worker) }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded bg-green-100 text-green-700 font-medium text-xs">✓ Aprobar</button>
                </form>
            @endif
            <a href="{{ route('admin.workers.edit', $worker) }}"
               class="px-3 py-1.5 rounded bg-brand-50 text-brand-700 font-medium text-xs">Editar</a>
            <a href="{{ route('workers.show', $worker->slug) }}" target="_blank"
               class="px-3 py-1.5 rounded bg-gray-100 text-gray-600 text-xs">Ver ↗</a>
            <form method="POST" action="{{ route('admin.workers.destroy', $worker) }}"
                  onsubmit="return confirm('¿Eliminar a {{ addslashes($worker->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded bg-red-50 text-red-600 text-xs">Eliminar</button>
            </form>
        </div>
    </div>
    @empty
    <p class="text-center text-gray-400 py-8">No hay trabajadores que coincidan con los filtros.</p>
    @endforelse
</div>

{{-- Desktop: tabla --}}
<div class="hidden sm:block bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Nombre</th>
                <th class="text-left px-4 py-3">Categoría</th>
                <th class="text-left px-4 py-3">Pueblo</th>
                <th class="text-left px-4 py-3">Cuenta</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-left px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workers as $worker)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">
                    {{ $worker->name }}
                    @if(!$worker->photo_path)
                        <span class="ml-1 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5">Sin foto</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $worker->categories->pluck('name')->join(', ') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $worker->town }}</td>
                <td class="px-4 py-3">
                    @if($worker->user)
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-medium text-green-700">🔑 Con cuenta</span>
                            <span class="text-xs text-gray-400">{{ $worker->user->email }}</span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400">— Sin cuenta</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $worker->is_active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $worker->is_active ? 'Activo' : 'Pendiente' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-3 flex-wrap">
                        @if(!$worker->is_active)
                            <form method="POST" action="{{ route('admin.workers.approve', $worker) }}">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium">✓ Aprobar</button>
                            </form>
                        @endif
                        <a href="{{ route('workers.show', $worker->slug) }}" target="_blank"
                           class="text-gray-400 hover:text-gray-600">↗</a>
                        <a href="{{ route('admin.workers.edit', $worker) }}"
                           class="text-brand-600 hover:text-brand-800">Editar</a>
                        <form method="POST" action="{{ route('admin.workers.destroy', $worker) }}"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($worker->name) }}? Esta acción no se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                    No hay trabajadores que coincidan con los filtros.
                    @if(!request()->hasAny(['busqueda','categoria','estado','cuenta']))
                        <a href="{{ route('admin.workers.create') }}" class="text-brand-600 hover:underline ml-1">Agregar el primero</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $workers->links() }}</div>

@endsection

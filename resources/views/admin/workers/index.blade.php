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

<div class="flex items-center justify-between mb-4">
    <div class="flex gap-2 text-sm">
        <a href="{{ route('admin.workers.index') }}"
           class="{{ !request('estado') ? 'font-semibold text-brand-600' : 'text-gray-500 hover:text-brand-600' }}">
            Todos ({{ $workers->total() }})
        </a>
        <span class="text-gray-300">|</span>
        <a href="?estado=pendiente"
           class="{{ request('estado') === 'pendiente' ? 'font-semibold text-brand-600' : 'text-gray-500 hover:text-brand-600' }}">
            Pendientes
        </a>
    </div>
    <a href="{{ route('admin.workers.create') }}"
       class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
        + Agregar trabajador
    </a>
</div>

{{-- Mobile: tarjetas --}}
<div class="sm:hidden space-y-3">
    @forelse($workers as $worker)
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <div>
                <div class="flex items-center gap-2">
                    <p class="font-semibold text-gray-900">{{ $worker->name }}</p>
                    @if(!$worker->photo_path)
                        <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5">Sin foto</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">{{ $worker->categories->pluck('name')->join(', ') }} · {{ $worker->town }}</p>
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
    <p class="text-center text-gray-400 py-8">No hay trabajadores aún.</p>
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
                <th class="text-left px-4 py-3">Recomend.</th>
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
                <td class="px-4 py-3 text-gray-600">
                    👍 {{ $worker->recommendations_count }}
                    <span class="text-gray-400">/{{ $worker->ratings_count }}</span>
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
                    No hay trabajadores aún.
                    <a href="{{ route('admin.workers.create') }}" class="text-brand-600 hover:underline ml-1">Agregar el primero</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $workers->links() }}</div>

@endsection

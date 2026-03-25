@extends('layouts.admin')
@section('title', 'Calificaciones')

@section('content')

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Trabajador</th>
                <th class="text-left px-4 py-3">Puntuación</th>
                <th class="text-left px-4 py-3">Nombre</th>
                <th class="text-left px-4 py-3">Comentario</th>
                <th class="text-left px-4 py-3">Fecha</th>
                <th class="text-left px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ratings as $rating)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                <td class="px-4 py-3">
                    <a href="{{ route('workers.show', $rating->worker->slug) }}" target="_blank"
                       class="text-brand-600 hover:underline">
                        {{ $rating->worker->name }}
                    </a>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-yellow-500">{{ str_repeat('★', $rating->score) }}</span>
                    <span class="text-gray-300">{{ str_repeat('★', 5 - $rating->score) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $rating->reviewer_name ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $rating->comment ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $rating->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.ratings.destroy', $rating) }}"
                          onsubmit="return confirm('¿Eliminar esta calificación?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No hay calificaciones todavía.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $ratings->links() }}</div>

@endsection

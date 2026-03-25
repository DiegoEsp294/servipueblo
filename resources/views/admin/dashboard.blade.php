@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <div class="text-3xl font-bold text-brand-600">{{ $stats['total_workers'] }}</div>
        <div class="text-sm text-gray-500 mt-1">Total trabajadores</div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <div class="text-3xl font-bold text-green-600">{{ $stats['active_workers'] }}</div>
        <div class="text-sm text-gray-500 mt-1">Trabajadores activos</div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
        <div class="text-3xl font-bold text-yellow-500">{{ $stats['total_ratings'] }}</div>
        <div class="text-sm text-gray-500 mt-1">Calificaciones totales</div>
    </div>
</div>

{{-- Últimos trabajadores --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-gray-800">Últimos trabajadores registrados</h2>
        <a href="{{ route('admin.workers.create') }}" class="text-sm text-brand-600 hover:underline">+ Agregar</a>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Nombre</th>
                <th class="pb-2">Categoría</th>
                <th class="pb-2">Pueblo</th>
                <th class="pb-2">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($latestWorkers as $worker)
            <tr class="border-b border-gray-50 last:border-0">
                <td class="py-2">
                    <a href="{{ route('admin.workers.edit', $worker) }}" class="text-brand-600 hover:underline">
                        {{ $worker->name }}
                    </a>
                </td>
                <td class="py-2 text-gray-600">{{ $worker->categories->pluck('name')->join(', ') ?: '—' }}</td>
                <td class="py-2 text-gray-600">{{ $worker->town }}</td>
                <td class="py-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $worker->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $worker->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

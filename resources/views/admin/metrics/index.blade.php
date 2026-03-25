@extends('layouts.admin')
@section('title', 'Métricas')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Métricas del mes</h1>
    <p class="text-sm text-gray-500 mt-1">{{ now()->translatedFormat('F Y') }}</p>
</div>

{{-- Totales globales --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-brand-600">{{ number_format($totals['views']) }}</div>
        <div class="text-sm text-gray-500 mt-1">Vistas de perfil</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ number_format($totals['whatsapp']) }}</div>
        <div class="text-sm text-gray-500 mt-1">Contactos WhatsApp</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-blue-500">{{ number_format($totals['shares']) }}</div>
        <div class="text-sm text-gray-500 mt-1">Perfiles compartidos</div>
    </div>
</div>

{{-- Gráfico de actividad diaria --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-8">
    <h2 class="text-sm font-semibold text-gray-700 mb-4">Actividad últimos 30 días</h2>
    <div class="flex items-end gap-0.5 h-32" id="chart-bars">
        @php
            $days = collect();
            for ($i = 29; $i >= 0; $i--) {
                $days->push(now()->subDays($i)->format('Y-m-d'));
            }
            $maxVal = 1;
            foreach ($days as $day) {
                $total = isset($daily[$day]) ? $daily[$day]->sum('total') : 0;
                if ($total > $maxVal) $maxVal = $total;
            }
        @endphp
        @foreach($days as $day)
            @php
                $dayData = $daily[$day] ?? collect();
                $views    = $dayData->where('type', 'view')->sum('total');
                $whatsapp = $dayData->where('type', 'whatsapp_click')->sum('total');
                $total    = $views + $whatsapp;
                $height   = $maxVal > 0 ? round(($total / $maxVal) * 100) : 0;
            @endphp
            <div class="flex-1 flex flex-col items-center justify-end group relative" title="{{ $day }}: {{ $total }} eventos">
                <div class="w-full bg-brand-500 rounded-sm opacity-80 group-hover:opacity-100 transition-opacity"
                     style="height: {{ $height }}%"></div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-between text-xs text-gray-400 mt-1">
        <span>{{ now()->subDays(29)->format('d M') }}</span>
        <span>Hoy</span>
    </div>
</div>

{{-- Tabla por trabajador --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50">
        <h2 class="text-sm font-semibold text-gray-700">Rendimiento por trabajador — este mes</h2>
    </div>

    {{-- Mobile: tarjetas --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @foreach($workers as $worker)
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $worker->name }}</p>
                    <p class="text-xs text-gray-400">{{ $worker->category->name }} · {{ $worker->town }}</p>
                </div>
                <a href="{{ route('admin.metrics.worker', $worker) }}"
                   class="text-xs text-brand-600 hover:underline">Ver detalle →</a>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-gray-50 rounded p-2">
                    <div class="font-semibold text-gray-800">{{ $worker->views_month }}</div>
                    <div class="text-xs text-gray-400">Vistas</div>
                </div>
                <div class="bg-green-50 rounded p-2">
                    <div class="font-semibold text-green-700">{{ $worker->whatsapp_month }}</div>
                    <div class="text-xs text-gray-400">WhatsApp</div>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    @php $conv = $worker->views_month > 0 ? round(($worker->whatsapp_month / $worker->views_month) * 100) : 0; @endphp
                    <div class="font-semibold text-gray-800">{{ $conv }}%</div>
                    <div class="text-xs text-gray-400">Conversión</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Desktop: tabla --}}
    <table class="hidden sm:table w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="text-left px-5 py-3">Trabajador</th>
                <th class="text-center px-4 py-3">Vistas (mes)</th>
                <th class="text-center px-4 py-3">WhatsApp (mes)</th>
                <th class="text-center px-4 py-3">Shares (total)</th>
                <th class="text-center px-4 py-3">Conversión</th>
                <th class="text-center px-4 py-3">Vistas (total)</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($workers as $worker)
            @php $conv = $worker->views_month > 0 ? round(($worker->whatsapp_month / $worker->views_month) * 100) : 0; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                    <div class="text-xs text-gray-400">{{ $worker->category->name }} · {{ $worker->town }}</div>
                </td>
                <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $worker->views_month }}</td>
                <td class="px-4 py-3 text-center font-semibold text-green-600">{{ $worker->whatsapp_month }}</td>
                <td class="px-4 py-3 text-center text-blue-500">{{ $worker->shares_total }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $conv >= 20 ? 'bg-green-100 text-green-700' : ($conv >= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500') }}">
                        {{ $conv }}%
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $worker->views_total }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.metrics.worker', $worker) }}"
                       class="text-brand-600 hover:underline text-xs">Ver →</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

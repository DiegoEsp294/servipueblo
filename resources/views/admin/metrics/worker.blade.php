@extends('layouts.admin')
@section('title', 'Métricas — ' . $worker->name)

@section('content')

<a href="{{ route('admin.metrics.index') }}" class="text-sm text-brand-600 hover:underline mb-4 inline-block">← Volver a métricas</a>

<div class="flex items-start gap-4 mb-6">
    <img src="{{ $worker->photo_url }}" class="w-14 h-14 rounded-full object-cover shrink-0">
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ $worker->name }}</h1>
        <p class="text-sm text-gray-500">{{ $worker->category->name }} · {{ $worker->town }}</p>
    </div>
    <a href="{{ route('workers.show', $worker->slug) }}" target="_blank"
       class="ml-auto text-xs text-gray-400 hover:text-brand-600">Ver perfil público ↗</a>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-brand-600">{{ $totals['views_month'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Vistas este mes</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $totals['whatsapp_month'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Contactos este mes</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-gray-700">{{ $totals['views'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Vistas históricas</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-gray-700">{{ $totals['whatsapp'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Contactos históricos</div>
    </div>
</div>

{{-- Tasa de conversión --}}
@php
    $conv = $totals['views_month'] > 0
        ? round(($totals['whatsapp_month'] / $totals['views_month']) * 100)
        : 0;
@endphp
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Tasa de conversión este mes</span>
        <span class="text-lg font-bold {{ $conv >= 20 ? 'text-green-600' : ($conv >= 10 ? 'text-yellow-600' : 'text-gray-500') }}">
            {{ $conv }}%
        </span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-3">
        <div class="h-3 rounded-full {{ $conv >= 20 ? 'bg-green-500' : ($conv >= 10 ? 'bg-yellow-400' : 'bg-gray-400') }}"
             style="width: {{ min($conv, 100) }}%"></div>
    </div>
    <p class="text-xs text-gray-400 mt-2">
        De cada 100 personas que ven el perfil, {{ $conv }} contactan por WhatsApp.
    </p>
</div>

{{-- Gráfico diario --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="flex items-center gap-4 mb-4">
        <span class="text-sm font-semibold text-gray-700">Actividad últimos 30 días</span>
        <div class="flex gap-3 text-xs text-gray-400">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-brand-500 inline-block"></span> Vistas</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span> WhatsApp</span>
        </div>
    </div>
    <div class="flex items-end gap-0.5 h-36">
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
                $dayData  = $daily[$day] ?? collect();
                $views    = $dayData->where('type', 'view')->sum('total');
                $whatsapp = $dayData->where('type', 'whatsapp_click')->sum('total');
                $total    = $views + $whatsapp;
                $hViews   = $maxVal > 0 ? round(($views / $maxVal) * 100) : 0;
                $hWa      = $maxVal > 0 ? round(($whatsapp / $maxVal) * 100) : 0;
            @endphp
            <div class="flex-1 flex flex-col items-center justify-end gap-px group relative"
                 title="{{ \Carbon\Carbon::parse($day)->format('d/m') }}: {{ $views }} vistas, {{ $whatsapp }} WhatsApp">
                <div class="w-full bg-green-400 rounded-sm" style="height: {{ $hWa }}%"></div>
                <div class="w-full bg-brand-400 rounded-sm" style="height: {{ $hViews }}%"></div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-between text-xs text-gray-400 mt-1">
        <span>{{ now()->subDays(29)->format('d M') }}</span>
        <span>Hoy</span>
    </div>
</div>

{{-- Tabla diaria detallada --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50">
        <h2 class="text-sm font-semibold text-gray-700">Detalle por día</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs">
            <tr>
                <th class="text-left px-5 py-2">Día</th>
                <th class="text-center px-4 py-2">Vistas</th>
                <th class="text-center px-4 py-2">WhatsApp</th>
                <th class="text-center px-4 py-2">Shares</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach(array_reverse($days->toArray()) as $day)
            @php
                $dayData  = $daily[$day] ?? collect();
                $views    = $dayData->where('type', 'view')->sum('total');
                $whatsapp = $dayData->where('type', 'whatsapp_click')->sum('total');
                $shares   = $dayData->where('type', 'share_click')->sum('total');
            @endphp
            @if($views + $whatsapp + $shares > 0)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2 text-gray-600">{{ \Carbon\Carbon::parse($day)->translatedFormat('d \d\e F') }}</td>
                <td class="px-4 py-2 text-center text-gray-800">{{ $views ?: '—' }}</td>
                <td class="px-4 py-2 text-center text-green-600 font-medium">{{ $whatsapp ?: '—' }}</td>
                <td class="px-4 py-2 text-center text-blue-500">{{ $shares ?: '—' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>

@endsection

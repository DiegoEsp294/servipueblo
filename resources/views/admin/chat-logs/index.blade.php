@extends('layouts.admin')
@section('title', 'Chat IA — Consultas sin respuesta')

@section('content')

{{-- Tarjetas de resumen --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm px-4 py-3">
        <div class="text-2xl font-bold text-gray-800">{{ $counts['total'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Total registros</div>
    </div>
    <div class="bg-white rounded-lg border border-orange-100 shadow-sm px-4 py-3">
        <div class="text-2xl font-bold text-orange-600">{{ $counts['pending'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Sin revisar</div>
    </div>
    <div class="bg-white rounded-lg border border-red-100 shadow-sm px-4 py-3">
        <div class="text-2xl font-bold text-red-500">{{ $counts['no_workers'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Sin trabajadores</div>
    </div>
    <div class="bg-white rounded-lg border border-yellow-100 shadow-sm px-4 py-3">
        <div class="text-2xl font-bold text-yellow-500">{{ $counts['ai_fail'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">IA no pudo ayudar</div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <select name="reason" class="text-sm border border-gray-200 rounded px-3 py-2 bg-white">
        <option value="">Todos los motivos</option>
        <option value="no_workers_found"    {{ request('reason') === 'no_workers_found'    ? 'selected' : '' }}>Sin trabajadores encontrados</option>
        <option value="ai_could_not_answer" {{ request('reason') === 'ai_could_not_answer' ? 'selected' : '' }}>IA no pudo responder</option>
        <option value="injection_attempt"   {{ request('reason') === 'injection_attempt'   ? 'selected' : '' }}>Intento de inyección</option>
    </select>
    <select name="reviewed" class="text-sm border border-gray-200 rounded px-3 py-2 bg-white">
        <option value="">Todos</option>
        <option value="0" {{ request('reviewed') === '0' ? 'selected' : '' }}>Sin revisar</option>
        <option value="1" {{ request('reviewed') === '1' ? 'selected' : '' }}>Revisados</option>
    </select>
    <button type="submit" class="text-sm bg-brand-600 text-white px-4 py-2 rounded hover:bg-brand-700">Filtrar</button>
    <a href="{{ route('admin.chat-logs.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:underline">Limpiar</a>
</form>

{{-- Tabla --}}
<div class="space-y-3">
    @forelse($logs as $log)
    <div class="bg-white rounded-lg border {{ $log->reviewed ? 'border-gray-100 opacity-70' : 'border-orange-200' }} shadow-sm p-4">
        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
            <div class="flex flex-wrap items-center gap-2">
                {{-- Motivo --}}
                @if($log->reason === 'no_workers_found')
                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Sin trabajadores</span>
                @elseif($log->reason === 'ai_could_not_answer')
                    <span class="text-xs font-medium bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">IA no ayudó</span>
                @elseif($log->reason === 'injection_attempt')
                    <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Inyección</span>
                @endif

                @if($log->reviewed)
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Revisado</span>
                @endif

                <span class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                @if($log->workers_found > 0)
                    <span class="text-xs text-gray-400">{{ $log->workers_found }} trabajador(es) en contexto</span>
                @endif
            </div>

            {{-- Acciones rápidas --}}
            <div class="flex gap-2 text-xs">
                <form method="POST" action="{{ route('admin.chat-logs.update', $log) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="reviewed" value="{{ $log->reviewed ? '0' : '1' }}">
                    <input type="hidden" name="admin_note" value="{{ $log->admin_note }}">
                    <button type="submit"
                            class="{{ $log->reviewed ? 'text-gray-400 hover:text-gray-600' : 'text-green-600 hover:text-green-800' }}">
                        {{ $log->reviewed ? 'Marcar pendiente' : 'Marcar revisado' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.chat-logs.destroy', $log) }}"
                      onsubmit="return confirm('¿Eliminar este registro?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600">Eliminar</button>
                </form>
            </div>
        </div>

        {{-- Mensaje del usuario --}}
        <div class="mb-2">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Consulta del usuario</div>
            <div class="text-sm font-medium text-gray-800 bg-gray-50 rounded px-3 py-2">{{ $log->message }}</div>
        </div>

        {{-- Palabras clave detectadas --}}
        @if($log->context_words)
        <div class="mb-2">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Palabras clave extraídas</div>
            <div class="text-xs text-gray-600">{{ $log->context_words }}</div>
        </div>
        @endif

        {{-- Respuesta IA --}}
        @if($log->ai_reply)
        <details class="mb-2">
            <summary class="text-xs text-gray-400 uppercase tracking-wide cursor-pointer hover:text-gray-600">
                Respuesta de la IA (expandir)
            </summary>
            <div class="mt-1 text-sm text-gray-600 bg-blue-50 rounded px-3 py-2 whitespace-pre-wrap">{{ $log->ai_reply }}</div>
        </details>
        @endif

        {{-- Nota del admin --}}
        <details {{ $log->admin_note ? 'open' : '' }}>
            <summary class="text-xs text-gray-400 uppercase tracking-wide cursor-pointer hover:text-gray-600">
                Nota interna {{ $log->admin_note ? '(hay nota)' : '' }}
            </summary>
            <form method="POST" action="{{ route('admin.chat-logs.update', $log) }}" class="mt-1 flex gap-2">
                @csrf @method('PATCH')
                <input type="hidden" name="reviewed" value="{{ $log->reviewed ? '1' : '0' }}">
                <input type="text" name="admin_note" value="{{ $log->admin_note }}"
                       placeholder="Ej: falta categoría Muebles, agregar carpintero..."
                       class="flex-1 text-sm border border-gray-200 rounded px-3 py-1.5">
                <button type="submit" class="text-sm bg-gray-600 text-white px-3 py-1.5 rounded hover:bg-gray-700">
                    Guardar
                </button>
            </form>
        </details>
    </div>
    @empty
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm px-4 py-10 text-center text-gray-400">
        No hay registros de consultas fallidas todavía.
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $logs->links() }}</div>

@endsection

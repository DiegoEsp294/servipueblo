@extends('layouts.admin')
@section('title', 'Editar trabajador')

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.workers.index') }}" class="text-sm text-brand-600 hover:underline mb-4 inline-block">← Volver</a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.workers.update', $worker) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.workers._form')
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2.5 rounded transition-colors">
                    Actualizar trabajador
                </button>
                <a href="{{ route('admin.workers.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-5 py-2.5 rounded transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

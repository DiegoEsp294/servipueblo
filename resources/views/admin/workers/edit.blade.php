@extends('layouts.admin')
@section('title', 'Editar trabajador')

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.workers.index') }}" class="text-sm text-brand-600 hover:underline mb-4 inline-block">← Volver</a>

    {{-- ── Cuenta del trabajador ───────────────────────────────────────────── --}}
    @if(session('account_created'))
        @php $creds = session('account_created'); @endphp
        <div class="bg-green-50 border border-green-300 rounded-xl px-5 py-4 mb-4">
            <p class="font-semibold text-green-800 mb-2">✅ Cuenta creada / contraseña reseteada</p>
            <p class="text-sm text-green-700">Guardá estas credenciales — la contraseña no se vuelve a mostrar:</p>
            <div class="mt-2 bg-white border border-green-200 rounded-lg px-4 py-3 font-mono text-sm">
                <div><span class="text-gray-500">Email:</span> <strong>{{ $creds['email'] }}</strong></div>
                <div><span class="text-gray-500">Contraseña:</span> <strong>{{ $creds['password'] }}</strong></div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-xl px-5 py-3 mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">👤 Cuenta de acceso</h3>

        @if($worker->user)
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm text-gray-600">
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full px-3 py-1 text-xs font-medium">
                        ✓ Tiene cuenta
                    </span>
                    <span class="ml-2 text-gray-500">{{ $worker->user->email }}</span>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.workers.reset-password', $worker) }}">
                        @csrf
                        <button type="submit"
                                class="text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-200 px-3 py-1.5 rounded transition-colors">
                            🔑 Resetear contraseña
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.workers.delete-account', $worker) }}"
                          onsubmit="return confirm('¿Eliminar la cuenta de acceso de {{ $worker->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded transition-colors">
                            Eliminar cuenta
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="flex items-center justify-between flex-wrap gap-3">
                <p class="text-sm text-gray-500">
                    Sin cuenta de acceso.
                    @if(!$worker->email)
                        <span class="text-amber-600">Agregá un email al trabajador primero.</span>
                    @endif
                </p>
                @if($worker->email)
                    <form method="POST" action="{{ route('admin.workers.create-account', $worker) }}">
                        @csrf
                        <button type="submit"
                                class="text-sm bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded transition-colors">
                            Crear cuenta
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- ── Formulario principal ────────────────────────────────────────────── --}}
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

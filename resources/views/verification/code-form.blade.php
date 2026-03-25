@extends('layouts.app')

@section('title', 'Ingresá el código')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="text-center mb-6">
            <div class="text-4xl mb-2">🔢</div>
            <h1 class="text-xl font-bold text-gray-900">Ingresá el código</h1>
            <p class="text-sm text-gray-500 mt-1">
                Enviamos un código de 6 dígitos a <strong>{{ $email }}</strong>
            </p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.verify-code') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ old('redirect', $redirect) }}">
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Código de 6 dígitos</label>
                <input type="text" name="code" value="{{ old('code') }}"
                       placeholder="123456"
                       maxlength="6"
                       inputmode="numeric"
                       pattern="[0-9]{6}"
                       required autofocus
                       class="w-full border border-gray-300 rounded px-3 py-3 text-2xl text-center tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 @error('code') border-red-400 @enderror">
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-2.5 rounded transition-colors">
                Verificar
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('verification.email-form', ['redirect' => $redirect]) }}"
               class="text-sm text-brand-600 hover:underline">
                ¿No recibiste el código? Enviá de nuevo
            </a>
        </div>
    </div>
</div>
@endsection

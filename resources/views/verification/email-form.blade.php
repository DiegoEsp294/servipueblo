@extends('layouts.app')

@section('title', 'Verificá tu correo')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="text-center mb-6">
            <div class="text-4xl mb-2">✉️</div>
            <h1 class="text-xl font-bold text-gray-900">Verificá tu correo</h1>
            <p class="text-sm text-gray-500 mt-1">Te enviamos un código de 6 dígitos para confirmar que sos vos.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send-code') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tu correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="tucorreo@ejemplo.com"
                       required autofocus
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('email') border-red-400 @enderror">
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-2.5 rounded transition-colors">
                Enviar código
            </button>
        </form>

        <p class="text-xs text-gray-400 text-center mt-4">
            Solo usamos tu correo para verificar tu identidad. No enviamos spam.
        </p>
    </div>
</div>
@endsection

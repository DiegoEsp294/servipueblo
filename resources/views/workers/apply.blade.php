@extends('layouts.app')

@section('title', 'Registrate como trabajador')

@section('content')

<div class="max-w-lg mx-auto">

    <div class="text-center mb-8">
        <div class="text-4xl mb-3">🛠️</div>
        <h1 class="text-2xl font-bold text-gray-900">Registrate como trabajador</h1>
        <p class="text-gray-500 mt-2">Completá el formulario y te contactamos para activar tu perfil.</p>
        <p class="text-gray-400 text-sm mt-1">¿Tenés dudas? Escribinos a <a href="mailto:servipueblosoporte@gmail.com" class="text-brand-600 hover:underline">servipueblosoporte@gmail.com</a></p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl px-5 py-4 mb-6 text-center">
            <div class="text-2xl mb-2">✅</div>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/registrate-como-trabajador" class="grid gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tu nombre completo *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ej: Juan García"
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tu oficio *</label>
                <select name="category_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('category_id') border-red-400 @enderror">
                    <option value="">Seleccioná tu oficio</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono WhatsApp *</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                       placeholder="+549 11 1234 5678"
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('phone') border-red-400 @enderror">
                <p class="text-xs text-gray-400 mt-0.5">Con código de país. Ej: +5491112345678</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email (opcional)</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="tu@email.com"
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('email') border-red-400 @enderror">
                <p class="text-xs text-gray-400 mt-0.5">Para avisarte cuando alguien te contacte</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pueblo o localidad *</label>
                <input type="text" name="town" value="{{ old('town') }}" required
                       placeholder="Ej: San Marcos"
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('town') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción breve (opcional)</label>
                <textarea name="description" rows="3" maxlength="500"
                          placeholder="Contá brevemente qué servicios ofrecés y tu experiencia..."
                          class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-lg transition-colors">
                Enviar solicitud
            </button>

            <p class="text-xs text-center text-gray-400">
                Tu perfil será revisado y activado en las próximas 24 horas.
            </p>
        </form>
    </div>
</div>

@endsection

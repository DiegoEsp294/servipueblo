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

            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué tipo de perfil querés crear? *</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['worker' => ['🔧', 'Trabajador / Oficio', 'Plomero, electricista...'], 'entrepreneur' => ['🏪', 'Emprendimiento', 'Tienda, productos, servicios...']] as $val => [$icon, $lbl, $hint])
                        @php $checked = old('type', 'worker') === $val; @endphp
                        <label class="flex flex-col gap-0.5 border rounded-lg px-3 py-2.5 cursor-pointer transition-colors
                                      {{ $checked ? 'border-brand-400 bg-brand-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <input type="radio" name="type" value="{{ $val }}" {{ $checked ? 'checked' : '' }}
                                       class="text-brand-600 focus:ring-brand-500">
                                <span class="text-sm font-medium">{{ $icon }} {{ $lbl }}</span>
                            </span>
                            <span class="text-xs text-gray-400 pl-5">{{ $hint }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tu nombre completo *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ej: Juan García"
                       class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" id="category-label">Rubro *</label>

                {{-- Categorías para trabajadores --}}
                <select name="category_id" id="category-worker" required
                        class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('category_id') border-red-400 @enderror">
                    <option value="">Seleccioná tu oficio</option>
                    @foreach($workerCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Categorías para emprendedores --}}
                <select name="category_id" id="category-entrepreneur" disabled
                        class="hidden w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Seleccioná el rubro</option>
                    @foreach($entrepreneurCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                    <option value="">— Sin rubro específico —</option>
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

@push('scripts')
<script>
(function () {
    var radios = document.querySelectorAll('input[name="type"]');
    var selWorker = document.getElementById('category-worker');
    var selEntrepreneur = document.getElementById('category-entrepreneur');
    var label = document.getElementById('category-label');

    function switchCategory(type) {
        if (type === 'entrepreneur') {
            selWorker.classList.add('hidden'); selWorker.disabled = true; selWorker.required = false;
            selEntrepreneur.classList.remove('hidden'); selEntrepreneur.disabled = false; selEntrepreneur.required = true;
            label.textContent = 'Rubro del emprendimiento *';
        } else {
            selEntrepreneur.classList.add('hidden'); selEntrepreneur.disabled = true; selEntrepreneur.required = false;
            selWorker.classList.remove('hidden'); selWorker.disabled = false; selWorker.required = true;
            label.textContent = 'Tu oficio *';
        }
    }

    radios.forEach(function (r) {
        r.addEventListener('change', function () { switchCategory(this.value); });
    });

    // Estado inicial
    var checked = document.querySelector('input[name="type"]:checked');
    if (checked) switchCategory(checked.value);
})();
</script>
@endpush

@endsection

@extends('layouts.app')

@section('title', 'Mi perfil — ' . $worker->name)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}"
             class="w-16 h-16 rounded-full object-cover border-2 border-brand-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $worker->name }}</h1>
            <p class="text-sm text-gray-500">
                {{ $worker->is_entrepreneur ? '🏪 Emprendimiento' : '🔧 Oficio' }} ·
                {{ $worker->categories->pluck('name')->join(', ') }} ·
                📍 {{ $worker->town }}
            </p>
            <a href="{{ $worker->profile_url }}" target="_blank"
               class="text-xs text-brand-600 hover:underline">Ver mi perfil público →</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl px-5 py-4 mb-6 flex items-center gap-2">
            <span class="text-xl">✅</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-6 text-sm">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('worker.profile.update') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 grid gap-5">
        @csrf
        @method('PUT')

        {{-- Descripción --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="description" rows="4" maxlength="500"
                      placeholder="Contá qué servicios ofrecés, tu experiencia, zona donde trabajás..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('description', $worker->description) }}</textarea>
            <p class="text-xs text-gray-400 mt-0.5">Esta descripción aparece en tu perfil y la usa el asistente virtual para recomendarte.</p>
        </div>

        {{-- Teléfono + Email --}}
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono WhatsApp *</label>
                <input type="tel" name="phone" value="{{ old('phone', $worker->phone) }}" required
                       placeholder="+549 11 1234 5678"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('phone') border-red-400 @enderror">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email de contacto</label>
                <input type="email" name="email" value="{{ old('email', $worker->email) }}"
                       placeholder="tu@email.com"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <p class="text-xs text-amber-600 mt-0.5">⚠️ Si lo cambiás, usá el nuevo para ingresar la próxima vez</p>
            </div>
        </div>

        {{-- Tarifa (solo workers) + Experiencia --}}
        @if(!$worker->is_entrepreneur)
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa aproximada</label>
                <input type="text" name="rate_info" value="{{ old('rate_info', $worker->rate_info) }}"
                       placeholder="Ej: $5.000–$8.000/hora"
                       maxlength="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Años de experiencia</label>
                <input type="number" name="years_experience" value="{{ old('years_experience', $worker->years_experience) }}"
                       min="1" max="60" placeholder="Ej: 5"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>
        @endif

        {{-- Disponibilidad --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['available' => ['🟢', 'Disponible'], 'on_request' => ['🟡', 'Con aviso previo'], 'unavailable' => ['🔴', 'No disponible']] as $val => [$icon, $lbl])
                    @php $checked = old('availability', $worker->availability ?? 'available') === $val; @endphp
                    <label class="flex items-center gap-2 border rounded-lg px-3 py-2.5 cursor-pointer transition-colors
                                  {{ $checked ? 'border-brand-400 bg-brand-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="availability" value="{{ $val }}" {{ $checked ? 'checked' : '' }}
                               class="text-brand-600 focus:ring-brand-500">
                        <span class="text-sm">{{ $icon }} {{ $lbl }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Etiquetas --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Etiquetas <span class="text-gray-400 font-normal">(máx. 6 · ayudan al asistente a recomendarte)</span>
            </label>
            @php $selectedTagIds = old('tags', $worker->tags->pluck('id')->toArray()); @endphp
            @foreach($tagsGrouped as $group => $groupTags)
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-3 mb-1">
                    {{ \App\Models\Tag::$groupLabels[$group] ?? $group }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($groupTags as $tag)
                        @php $checked = in_array($tag->id, $selectedTagIds); @endphp
                        <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="tag-checkbox sr-only">
                            <span class="tag-pill px-2.5 py-1 rounded-full text-xs border transition-colors
                                         {{ $checked ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-300 hover:border-brand-400' }}">
                                {{ $tag->icon }} {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Horarios de atención --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Horarios de atención
                <span class="text-gray-400 font-normal">(opcional)</span>
            </label>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                @foreach($days as $day => $name)
                @php $h = $hoursByDay->get($day); @endphp
                <div class="flex items-center gap-3 px-3 py-2.5 border-b border-gray-100 last:border-0
                            {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    <span class="text-sm font-medium text-gray-700 w-20 shrink-0">{{ $name }}</span>

                    <label class="flex items-center gap-1.5 cursor-pointer shrink-0">
                        <input type="checkbox" name="hours[{{ $day }}][closed]" value="1"
                               class="day-closed-check accent-red-500"
                               data-day="{{ $day }}"
                               {{ optional($h)->is_closed ? 'checked' : '' }}>
                        <span class="text-xs text-gray-500">Cerrado</span>
                    </label>

                    <div class="flex items-center gap-1.5 flex-1 day-times-{{ $day }}
                                {{ optional($h)->is_closed ? 'opacity-30 pointer-events-none' : '' }}">
                        <input type="time" name="hours[{{ $day }}][open]"
                               value="{{ optional($h)->open_time }}"
                               class="flex-1 border border-gray-200 rounded px-2 py-1 text-sm text-center">
                        <span class="text-gray-400 text-xs">a</span>
                        <input type="time" name="hours[{{ $day }}][close]"
                               value="{{ optional($h)->close_time }}"
                               class="flex-1 border border-gray-200 rounded px-2 py-1 text-sm text-center">
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">Si no cargás horarios, no se muestran en tu perfil.</p>
        </div>

        {{-- Foto de perfil --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil</label>
            @if($worker->photo_path)
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ $worker->photo_url }}" class="w-14 h-14 rounded-full object-cover border border-gray-200">
                    <p class="text-xs text-gray-400">Subí una nueva foto para reemplazarla</p>
                </div>
            @else
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-2">
                    <span class="text-amber-500">📷</span>
                    <p class="text-xs text-amber-700 font-medium">Los perfiles con foto reciben 3× más contactos</p>
                </div>
            @endif
            <input type="file" name="photo" accept="image/*"
                   class="w-full text-sm text-gray-600 @error('photo') text-red-500 @enderror">
            <p class="text-xs text-gray-400 mt-0.5">JPG o PNG · Máx. 10MB</p>
            @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Fotos de trabajos --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fotos de trabajos (máx. 5)</label>

            @if($worker->photos->isNotEmpty())
                <p class="text-xs text-gray-400 mb-2">Marcá las que querés eliminar y guardá los cambios.</p>
                <div class="flex flex-wrap gap-3 mb-3">
                    @foreach($worker->photos as $photo)
                        <div class="flex flex-col items-center gap-1">
                            <img src="{{ $photo->url }}" class="w-24 h-24 object-cover rounded border border-gray-200"
                                 onerror="this.classList.add('opacity-30'); this.closest('div').querySelector('.broken-badge').classList.remove('hidden')">
                            <span class="broken-badge hidden text-xs text-red-500 font-medium">⚠ Error</span>
                            <label class="flex items-center gap-1 cursor-pointer select-none">
                                <input type="checkbox" name="delete_photos_check[]" value="{{ $photo->id }}"
                                       class="delete-photo-check accent-red-500">
                                <span class="text-xs text-red-600 font-medium">Eliminar</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="delete_photos" id="delete_photos_input" value="">
            @endif

            @php $slotsLeft = 5 - $worker->photos->count(); @endphp
            <input type="file" name="work_photos[]" accept="image/*" multiple
                   class="w-full text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $slotsLeft > 0 ? "Podés subir hasta {$slotsLeft} foto(s) más" : "Ya tenés 5 fotos — eliminá alguna para agregar nuevas" }}
                · JPG o PNG · Máx. 10MB c/u
            </p>
        </div>

        <button type="submit"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-lg transition-colors">
            Guardar cambios
        </button>
    </form>

    {{-- Link al muro --}}
    <div class="text-center mt-2">
        <a href="{{ route('worker.posts.index') }}"
           class="inline-flex items-center gap-2 text-sm text-brand-600 hover:underline font-medium">
            📢 Publicar novedades / Menú del día →
        </a>
    </div>

    {{-- Cerrar sesión --}}
    <div class="text-center mt-4">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                Cerrar sesión
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Tag pills
(function () {
    var MAX = 6;
    document.querySelectorAll('.tag-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var checked = document.querySelectorAll('.tag-checkbox:checked');
            if (this.checked && checked.length > MAX) { this.checked = false; return; }
            var pill = this.nextElementSibling;
            if (this.checked) {
                pill.classList.add('bg-brand-600','text-white','border-brand-600');
                pill.classList.remove('bg-white','text-gray-600','border-gray-300');
            } else {
                pill.classList.remove('bg-brand-600','text-white','border-brand-600');
                pill.classList.add('bg-white','text-gray-600','border-gray-300');
            }
        });
    });
})();

// Horarios — deshabilitar campos cuando se marca "Cerrado"
document.querySelectorAll('.day-closed-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var day = this.dataset.day;
        var block = document.querySelector('.day-times-' + day);
        if (this.checked) {
            block.classList.add('opacity-30', 'pointer-events-none');
        } else {
            block.classList.remove('opacity-30', 'pointer-events-none');
        }
    });
});

// Fotos borrar — escuchar change en cada checkbox
document.querySelectorAll('.delete-photo-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var ids = Array.from(document.querySelectorAll('.delete-photo-check:checked')).map(function(el){ return el.value; });
        document.getElementById('delete_photos_input').value = ids.join(',');
    });
});
</script>
@endpush

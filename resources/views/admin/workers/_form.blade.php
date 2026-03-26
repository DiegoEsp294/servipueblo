@php $worker = $worker ?? null; @endphp

<div class="grid gap-4">

    {{-- Nombre --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
        <input type="text" name="name" value="{{ old('name', optional($worker)->name) }}" required
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Categorías / Oficios (multi-select) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Oficios *
            <span class="font-normal text-gray-400">(el primero tildado = principal · hasta 5)</span>
        </label>
        @php
            $selectedIds  = old('category_ids', optional($worker)->categories ? $worker->categories->pluck('id')->toArray() : []);
            $primaryFirst = optional($worker)->categories
                ? $worker->categories->sortByDesc(fn($c) => $c->pivot->is_primary ?? false)->pluck('id')->toArray()
                : [];
        @endphp
        <div class="border border-gray-200 rounded p-3 grid grid-cols-2 sm:grid-cols-3 gap-2 @error('category_ids') border-red-400 @enderror"
             id="categories-grid">
            @foreach($categories as $cat)
                @php $checked = in_array($cat->id, $selectedIds); @endphp
                <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50
                              {{ $checked ? 'bg-brand-50 border border-brand-200' : 'border border-transparent' }}"
                       data-cat-label>
                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                           {{ $checked ? 'checked' : '' }}
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 cat-checkbox">
                    <span class="text-sm">{{ $cat->icon }} {{ $cat->name }}</span>
                </label>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-1" id="primary-hint">
            @if(!empty($selectedIds))
                Primario: <strong id="primary-name">{{ optional($categories->firstWhere('id', $selectedIds[0]))->name ?? '' }}</strong>
            @else
                El primero que tildés será el oficio principal.
            @endif
        </p>
        @error('category_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Descripción + Años de experiencia (en fila) --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción corta</label>
            <textarea name="description" rows="3" maxlength="500"
                      placeholder="Describe brevemente los servicios que ofrece..."
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('description', optional($worker)->description) }}</textarea>
        </div>
        <div class="flex flex-col gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa aproximada</label>
                <input type="text" name="rate_info"
                       value="{{ old('rate_info', optional($worker)->rate_info) }}"
                       placeholder="Ej: $5.000–$8.000/hora"
                       maxlength="100"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <p class="text-xs text-gray-400 mt-0.5">Opcional · visible en el perfil</p>
            </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Años de experiencia</label>
            <input type="number" name="years_experience"
                   value="{{ old('years_experience', optional($worker)->years_experience) }}"
                   min="1" max="60" placeholder="Ej: 5"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('years_experience') border-red-400 @enderror">
            <p class="text-xs text-gray-400 mt-0.5">Opcional · 1 a 60</p>
            @error('years_experience')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        </div>
    </div>

    {{-- Teléfono + Email (en fila) --}}
    <div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono WhatsApp *</label>
        <input type="text" name="phone" value="{{ old('phone', optional($worker)->phone) }}" required
               placeholder="+521234567890"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('phone') border-red-400 @enderror">
        <p class="text-xs text-gray-400 mt-0.5">Incluye código de país. Ej: +521234567890</p>
        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email de notificaciones</label>
        <input type="email" name="email" value="{{ old('email', optional($worker)->email) }}"
               placeholder="juan@ejemplo.com"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
        <p class="text-xs text-gray-400 mt-0.5">Recibe aviso cuando alguien lo contacta</p>
    </div>
    </div>

    {{-- Pueblo --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pueblo / Localidad *</label>
        <input type="text" name="town" value="{{ old('town', optional($worker)->town) }}" required
               placeholder="Ej: San Marcos"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('town') border-red-400 @enderror">
        @error('town')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Foto --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil</label>
        @if(optional($worker)->photo_path)
            <img src="{{ $worker->photo_url }}" alt="Foto actual" class="w-16 h-16 rounded-full object-cover mb-2">
        @else
            <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-2">
                <span class="text-amber-500 text-lg">📷</span>
                <p class="text-xs text-amber-700 font-medium">Los perfiles con foto reciben 3× más contactos</p>
            </div>
        @endif
        <input type="file" name="photo" accept="image/*"
               class="w-full text-sm text-gray-600 @error('photo') text-red-500 @enderror">
        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG o GIF · Máximo 2MB</p>
        @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Fotos de trabajos --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Fotos de trabajos realizados (máx. 2)</label>

        @if(optional($worker)->photos && $worker->photos->count())
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($worker->photos as $photo)
                    <div class="relative group">
                        <img src="{{ $photo->url }}" class="w-24 h-24 object-cover rounded border border-gray-200">
                        <label class="absolute inset-0 bg-red-500 bg-opacity-0 group-hover:bg-opacity-60 rounded flex items-center justify-center cursor-pointer transition-all">
                            <input type="checkbox" name="delete_photos_check[]" value="{{ $photo->id }}"
                                   class="hidden delete-photo-check">
                            <span class="text-white text-xs font-bold opacity-0 group-hover:opacity-100">✕ Borrar</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="delete_photos" id="delete_photos_input" value="">
            <p class="text-xs text-gray-400 mb-2">Hover sobre una foto y tildala para eliminarla al guardar.</p>
        @endif

        @php $slotsLeft = 2 - (optional($worker)->photos ? $worker->photos->count() : 0); @endphp
        @if($slotsLeft > 0)
            <input type="file" name="work_photos[]" accept="image/*" multiple
                   class="w-full text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-0.5">Podés subir hasta {{ $slotsLeft }} foto(s) más · JPG o PNG · Máx. 2MB c/u</p>
        @else
            <p class="text-xs text-gray-400">Ya tiene 2 fotos. Eliminá alguna para agregar nuevas.</p>
        @endif
    </div>

    {{-- Disponibilidad --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach(['available' => ['🟢', 'Disponible'], 'on_request' => ['🟡', 'Con aviso previo'], 'unavailable' => ['🔴', 'No disponible']] as $val => [$icon, $lbl])
                @php $checked = old('availability', optional($worker)->availability ?? 'available') === $val; @endphp
                <label class="flex items-center gap-2 border rounded-lg px-3 py-2.5 cursor-pointer transition-colors
                              {{ $checked ? 'border-brand-400 bg-brand-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" name="availability" value="{{ $val }}" {{ $checked ? 'checked' : '' }}
                           class="text-brand-600 focus:ring-brand-500">
                    <span class="text-sm">{{ $icon }} {{ $lbl }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Activo --}}
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', optional($worker)->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <label for="is_active" class="text-sm text-gray-700">Mostrar en el directorio (activo)</label>
    </div>

</div>

@push('scripts')
<script>
    // Fotos de trabajo — selección para borrar
    document.querySelectorAll('.delete-photo-check').forEach(function(cb) {
        cb.closest('label').addEventListener('click', function() {
            cb.checked = !cb.checked;
            var ids = Array.from(document.querySelectorAll('.delete-photo-check:checked')).map(function(el) { return el.value; });
            document.getElementById('delete_photos_input').value = ids.join(',');
        });
    });

    // Categorías — resaltar primaria y límite visual
    (function() {
        var grid   = document.getElementById('categories-grid');
        var hint   = document.getElementById('primary-hint');
        if (!grid) return;

        function updatePrimary() {
            var checked = grid.querySelectorAll('.cat-checkbox:checked');
            var labels  = grid.querySelectorAll('[data-cat-label]');

            // Límite de 5
            if (checked.length >= 5) {
                grid.querySelectorAll('.cat-checkbox:not(:checked)').forEach(function(cb) {
                    cb.disabled = true;
                });
            } else {
                grid.querySelectorAll('.cat-checkbox').forEach(function(cb) {
                    cb.disabled = false;
                });
            }

            // Estilos
            labels.forEach(function(lbl) {
                var cb = lbl.querySelector('.cat-checkbox');
                if (cb.checked) {
                    lbl.classList.add('bg-brand-50', 'border-brand-200');
                    lbl.classList.remove('border-transparent');
                } else {
                    lbl.classList.remove('bg-brand-50', 'border-brand-200');
                    lbl.classList.add('border-transparent');
                }
            });

            // Hint de primaria
            if (hint) {
                if (checked.length > 0) {
                    var firstName = checked[0].closest('[data-cat-label]').querySelector('span').textContent.trim();
                    hint.innerHTML = 'Primario: <strong>' + firstName + '</strong>';
                } else {
                    hint.textContent = 'El primero que tildés será el oficio principal.';
                }
            }
        }

        grid.querySelectorAll('.cat-checkbox').forEach(function(cb) {
            cb.addEventListener('change', updatePrimary);
        });

        updatePrimary();
    })();
</script>
@endpush

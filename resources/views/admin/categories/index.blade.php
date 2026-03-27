@extends('layouts.admin')
@section('title', 'Categorías')

@section('content')

<div class="flex flex-col gap-6">

    {{-- Agregar categoría — arriba en mobile --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Agregar categoría</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ej: Gasero"
                           class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Para qué tipo *</label>
                    <select name="for_type" class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="worker" {{ old('for_type') === 'worker' ? 'selected' : '' }}>🔧 Trabajadores / Oficios</option>
                        <option value="entrepreneur" {{ old('for_type') === 'entrepreneur' ? 'selected' : '' }}>🏪 Emprendimientos</option>
                        <option value="all" {{ old('for_type') === 'all' ? 'selected' : '' }}>🗂️ Ambos</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ícono (emoji, opcional)</label>
                <div class="flex items-center gap-3 mb-2">
                    <span id="emoji-preview" class="text-4xl leading-none">{{ old('icon') ?: '❔' }}</span>
                    <input type="text" name="icon" id="icon-input" value="{{ old('icon') }}"
                           placeholder="Pegá o escribí un emoji"
                           maxlength="10"
                           class="flex-1 border border-gray-300 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-2">
                    <p class="text-xs text-gray-400 mb-2">O elegí uno de acá:</p>
                    <div class="flex flex-wrap gap-0.5">
                        @foreach(['⚡','🔧','🪚','🔥','🔑','🌿','🔩','🧵','💅','✂','🚗','🧹','💻','👶','🐛','📸','🚛','🏥','🥖','🫓','🌽','🥩','🛒','👗','👕','🪡','🎨','🍭','🎉','🌱','🧴','🥚','🧃','🏠','🍕','🎂','🐾','🚿','🪴','🧰','🛠','🎓','📦','🎵','💈','🍺','🛵','🚑','🧲','💊','📱','🏪','🌾','🐄','🐓','🎪','🎭','🏊','⚽','🎮','📚','🖼','🪑','🛏','🔬','⚙️'] as $e)
                            <button type="button" onclick="pickEmoji('{{ $e }}')"
                                    class="text-2xl p-1.5 hover:bg-white rounded transition-colors">{{ $e }}</button>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">El emoji de la izquierda muestra cómo se va a ver.</p>
            </div>

            <button type="submit"
                    class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2.5 rounded transition-colors">
                Agregar categoría
            </button>
        </form>
    </div>

    {{-- Lista de categorías --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Categoría</th>
                        <th class="text-left px-4 py-3 hidden sm:table-cell">Tipo</th>
                        <th class="text-left px-4 py-3 hidden sm:table-cell">Perfiles</th>
                        <th class="text-left px-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $category->icon }} {{ $category->name }}</span>
                            {{-- En mobile mostramos el tipo bajo el nombre --}}
                            <div class="sm:hidden mt-0.5">
                                @php $ft = $category->for_type ?? 'worker'; @endphp
                                <span class="text-xs text-gray-400">
                                    {{ $ft === 'worker' ? '🔧 Oficio' : ($ft === 'entrepreneur' ? '🏪 Emprendimiento' : '🗂️ Ambos') }}
                                    · {{ $category->workers_count }} perfiles
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @php $ft = $category->for_type ?? 'worker'; @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $ft === 'worker' ? 'bg-blue-50 text-blue-600' : ($ft === 'entrepreneur' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600') }}">
                                {{ $ft === 'worker' ? '🔧 Oficio' : ($ft === 'entrepreneur' ? '🏪 Emprendimiento' : '🗂️ Ambos') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $category->workers_count }}</td>
                        <td class="px-4 py-3">
                            @if($category->workers_count === 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($category->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Eliminar</button>
                                </form>
                            @else
                                <span class="text-gray-300 text-xs">En uso</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No hay categorías.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function pickEmoji(e) {
    document.getElementById('icon-input').value = e;
    document.getElementById('emoji-preview').textContent = e;
}
document.getElementById('icon-input').addEventListener('input', function () {
    document.getElementById('emoji-preview').textContent = this.value || '❔';
});
</script>
@endpush

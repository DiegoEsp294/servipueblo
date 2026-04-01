@extends('layouts.app')
@section('title', 'Mis novedades — ' . $worker->name)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('worker.profile.edit') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Mi perfil</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Novedades</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- Formulario nueva novedad --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">📢 Nueva publicación</h2>
        <form method="POST" action="{{ route('worker.posts.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <textarea name="content" rows="3" maxlength="500" required
                      placeholder="Ej: Menú del día: milanesa con puré $2.500 🍽️  Pedidos hasta las 11hs al WhatsApp."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('content') }}</textarea>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[160px]">
                    <input type="file" name="photo" accept="image/*" class="text-sm text-gray-500">
                    <p class="text-xs text-gray-400">Foto opcional · Máx. 5MB</p>
                </div>
                <div class="shrink-0">
                    <label class="block text-xs text-gray-500 mb-1">¿Hasta cuándo?</label>
                    <select name="duration" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="today">Hoy (expira a medianoche)</option>
                        <option value="3days">3 días</option>
                        <option value="7days" selected>7 días</option>
                        <option value="none">Sin vencimiento</option>
                    </select>
                </div>
                <button type="submit"
                        class="shrink-0 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    Publicar
                </button>
            </div>
        </form>
    </div>

    {{-- Lista de novedades --}}
    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Tus publicaciones</h2>

    <div class="space-y-3">
        @forelse($posts as $post)
        <div class="bg-white rounded-xl border {{ $post->is_sold_out ? 'border-gray-200 opacity-60' : 'border-gray-100' }} shadow-sm p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                    @if($post->is_expired)
                        <span class="text-xs bg-gray-100 text-gray-400 rounded-full px-2 py-0.5">Vencida</span>
                    @elseif($post->expiry_label)
                        <span class="text-xs bg-amber-50 text-amber-600 rounded-full px-2 py-0.5">{{ $post->expiry_label }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Compartir --}}
                    <a href="{{ route('worker.post.show', [$worker->slug, $post->id]) }}"
                       target="_blank"
                       class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                        Compartir
                    </a>
                    {{-- Agotado toggle --}}
                    <form method="POST" action="{{ route('worker.posts.sold-out', $post) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="text-xs {{ $post->is_sold_out ? 'text-gray-400' : 'text-orange-500 hover:text-orange-700' }} font-medium">
                            {{ $post->is_sold_out ? '✓ Agotado' : 'Marcar agotado' }}
                        </button>
                    </form>
                    {{-- Eliminar --}}
                    <form method="POST" action="{{ route('worker.posts.destroy', $post) }}"
                          onsubmit="return confirm('¿Eliminar esta publicación?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
                    </form>
                </div>
            </div>

            @if($post->is_sold_out)
                <span class="inline-block text-xs font-bold bg-red-100 text-red-600 rounded-full px-2 py-0.5 mb-2">
                    ⚠ AGOTADO
                </span>
            @endif

            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $post->content }}</p>

            @if($post->photo_url)
                <img src="{{ $post->photo_url }}" alt="Foto"
                     class="mt-3 rounded-lg max-h-64 object-cover w-full">
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl border border-dashed border-gray-200 p-8 text-center text-gray-400">
            <p class="text-3xl mb-2">📢</p>
            <p class="text-sm">Todavía no publicaste nada.</p>
            <p class="text-xs mt-1">Contales a tus clientes el menú del día, ofertas o novedades.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>

</div>
@endsection

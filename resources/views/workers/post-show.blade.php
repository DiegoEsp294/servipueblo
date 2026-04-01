@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    $ogTitle       = $worker->name . ' — ' . Str::limit($post->content, 70);
    $ogDescription = $post->content;
    $postUrl       = route('worker.post.show', [$worker->slug, $post->id]);
    $imageUrl      = route('worker.post.image', [$worker->slug, $post->id]);
@endphp

@section('title', $ogTitle)
@section('description', Str::limit($ogDescription, 160))
@section('og_title', $ogTitle)
@section('og_description', $ogDescription)
@section('og_image', $imageUrl)
@section('og_image_width', '1080')
@section('og_image_height', '1080')
@section('og_type', 'article')

@push('meta')
<meta property="article:author" content="{{ $worker->name }}">
@endpush

@section('content')
<div class="max-w-lg mx-auto">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1.5">
        <a href="{{ $worker->profile_url }}" class="hover:text-brand-600">{{ $worker->name }}</a>
        <span>›</span>
        <span class="text-gray-600">Novedad</span>
    </nav>

    {{-- Card de la novedad --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">

        {{-- Header del trabajador --}}
        <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-gray-100">
            <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}"
                 class="w-12 h-12 rounded-full object-cover bg-gray-100 shrink-0">
            <div>
                <a href="{{ $worker->profile_url }}"
                   class="font-semibold text-gray-900 hover:text-brand-600 leading-tight block">
                    {{ $worker->name }}
                </a>
                <p class="text-xs text-gray-500">
                    {{ $worker->categories->pluck('name')->join(' · ') }}
                    @if($worker->town) · {{ $worker->town }} @endif
                </p>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="px-5 py-4">
            @if($post->is_sold_out)
                <span class="inline-block text-xs font-bold bg-red-100 text-red-600 rounded-full px-2 py-0.5 mb-2">
                    ⚠ AGOTADO
                </span>
            @endif
            <p class="text-gray-800 text-base whitespace-pre-wrap leading-relaxed">{{ $post->content }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ $post->created_at->diffForHumans() }}</p>
        </div>

        @if($post->photo_url)
            <img src="{{ $post->photo_url }}" alt="Foto de la novedad"
                 class="w-full max-h-96 object-cover">
        @endif

        {{-- Link al perfil --}}
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
            <a href="{{ $worker->profile_url }}"
               class="text-sm text-brand-600 hover:underline font-medium">
                Ver perfil completo →
            </a>
        </div>
    </div>

    {{-- Compartir --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Compartir esta novedad</h2>

        <div class="grid grid-cols-2 gap-3">

            {{-- WhatsApp --}}
            <a href="https://wa.me/?text={{ urlencode($worker->name . ': ' . Str::limit($post->content, 80) . "\n" . $postUrl) }}"
               target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>

            {{-- Facebook --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($postUrl) }}"
               target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>

            {{-- Copiar link --}}
            <button onclick="copyLink()"
                    class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span id="copyBtn">Copiar link</span>
            </button>

            {{-- Descargar imagen (para Instagram) --}}
            <a href="{{ $imageUrl }}" download="novedad-{{ $worker->slug }}.png"
               class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Para Instagram
            </a>
        </div>

        <p class="text-xs text-gray-400 mt-3 text-center">
            La imagen descargada ya viene lista para subir a Instagram o Stories.
        </p>
    </div>

</div>

<script>
function copyLink() {
    navigator.clipboard.writeText('{{ $postUrl }}').then(function() {
        var btn = document.getElementById('copyBtn');
        btn.textContent = '¡Copiado!';
        setTimeout(function() { btn.textContent = 'Copiar link'; }, 2000);
    });
}
</script>
@endsection

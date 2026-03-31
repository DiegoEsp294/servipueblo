@extends('layouts.app')

@section('title', $metaTitle)
@section('description', $metaDescription)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)

@section('content')

{{-- JSON-LD para Google --}}
@if($workers->isNotEmpty())
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "{{ $titleBase }}",
  "description": "{{ $metaDescription }}",
  "numberOfItems": {{ $workers->count() }},
  "itemListElement": [
    @foreach($workers as $i => $w)
    {
      "@type": "ListItem",
      "position": {{ $i + 1 }},
      "item": {
        "@type": "LocalBusiness",
        "name": "{{ $w->name }}",
        "description": "{{ addslashes($w->description ?? $category->name) }}",
        "url": "{{ url($w->profile_url) }}",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "{{ $w->town }}",
          "addressCountry": "AR"
        }
        @if($w->phone), "telephone": "{{ $w->phone }}" @endif
        @if($w->photo_path), "image": "{{ $w->photo_url }}" @endif
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endif

{{-- Breadcrumb --}}
<nav class="text-xs text-gray-400 mb-4 flex items-center gap-1.5">
    <a href="{{ route('workers.index') }}" class="hover:text-brand-600">Directorio</a>
    <span>›</span>
    @if($town)
        <a href="{{ route('category.landing', $category->slug) }}" class="hover:text-brand-600">{{ $category->name }}</a>
        <span>›</span>
        <span class="text-gray-600">{{ $town }}</span>
    @else
        <span class="text-gray-600">{{ $category->name }}</span>
    @endif
</nav>

{{-- Hero --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">
        {{ $category->emoji ?? '' }} {{ $titleBase }}
    </h1>
    <p class="text-gray-500 text-sm">
        @if($workers->count() > 0)
            {{ $workers->count() }} {{ $workers->count() === 1 ? 'profesional disponible' : 'profesionales disponibles' }}
            · Contacto directo por WhatsApp
        @else
            Todavía no hay registros en esta categoría. ¡Pronto habrá más!
        @endif
    </p>
</div>

{{-- Filtro por pueblo (si hay varios) --}}
@if($towns->count() > 1)
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('category.landing', $category->slug) }}"
       class="text-xs px-3 py-1.5 rounded-full border transition-colors
              {{ !$town ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-300 hover:border-brand-400' }}">
        Todos los pueblos
    </a>
    @foreach($towns as $t)
    <a href="{{ route('category.landing.town', [$category->slug, \Illuminate\Support\Str::slug($t)]) }}"
       class="text-xs px-3 py-1.5 rounded-full border transition-colors
              {{ $town === $t ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-300 hover:border-brand-400' }}">
        {{ $t }}
    </a>
    @endforeach
</div>
@endif

{{-- Listado --}}
@if($workers->isNotEmpty())
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($workers as $worker)
        @include('components.worker-card', ['worker' => $worker])
    @endforeach
</div>
@else
<div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
    <p class="text-4xl mb-3">🔍</p>
    <p class="text-gray-600 font-medium">No encontramos {{ strtolower($category->name) }} en {{ $town ?? 'esta zona' }}</p>
    <p class="text-sm text-gray-400 mt-1">¿Sos {{ strtolower($category->name) }}? <a href="{{ route('workers.apply') }}" class="text-brand-600 hover:underline">Registrate gratis</a></p>
</div>
@endif

{{-- Otras categorías relacionadas o CTA --}}
<div class="mt-8 pt-6 border-t border-gray-100 text-center">
    <p class="text-sm text-gray-400 mb-3">¿Buscás otro servicio?</p>
    <a href="{{ route('workers.index') }}"
       class="inline-block bg-white border border-gray-200 hover:border-brand-400 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
        Ver todos los servicios →
    </a>
</div>

@endsection

@extends('layouts.app')

@php
    $categoryNames = $worker->categories->pluck('name')->join(', ');
    $primaryCat    = $worker->category; // accessor → primaria
    $ogTitle       = $worker->name . ' · ' . ($primaryCat->icon ?? '') . ' ' . $categoryNames . ' en ' . $worker->town . ' | ServiPueblo';
    $recText       = $worker->ratings_count > 0
        ? $worker->recommendations_count . ' de ' . $worker->ratings_count . ' personas lo recomiendan. '
        : '';
    $ogDescription = $recText
        . ($worker->description ?? $worker->name . ' ofrece servicios de ' . $categoryNames . ' en ' . $worker->town . '.')
        . ' Contactalo por WhatsApp directo en ServiPueblo.';
    $ogImage       = $worker->photo_path
        ? url(\Illuminate\Support\Facades\Storage::url($worker->photo_path))
        : url('images/og-default.png');
@endphp

@section('title', $worker->name . ' — ' . $categoryNames . ' en ' . $worker->town)
@section('description', $ogDescription)
@section('og_type', 'profile')
@section('og_title', $ogTitle)
@section('og_description', $ogDescription)
@section('og_image', $ogImage)
@section('og_image_width', '400')
@section('og_image_height', '400')

@push('meta')
<meta property="profile:first_name" content="{{ explode(' ', $worker->name)[0] }}">
<meta property="profile:last_name" content="{{ implode(' ', array_slice(explode(' ', $worker->name), 1)) }}">
<meta name="keywords" content="{{ $categoryNames }}, {{ $worker->town }}, servicios, trabajadores, ServiPueblo">
<link rel="canonical" href="{{ $worker->profile_url }}">

{{-- JSON-LD para Google --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "{{ $worker->is_entrepreneur ? 'LocalBusiness' : 'Person' }}",
  "name": "{{ $worker->name }}",
  @if($worker->is_entrepreneur)
  "description": "{{ addslashes($ogDescription) }}",
  @else
  "jobTitle": "{{ $categoryNames }}",
  @endif
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "{{ $worker->town }}",
    "addressRegion": "Santiago del Estero",
    "addressCountry": "AR"
  },
  "url": "{{ url($worker->profile_url) }}",
  "image": "{{ $ogImage }}",
  "description": "{{ addslashes($ogDescription) }}",
  "sameAs": ["{{ $worker->whatsapp_url }}"]
  @if($worker->ratings_count > 0)
  ,"aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $worker->average_rating }}",
    "reviewCount": "{{ $worker->ratings_count }}",
    "bestRating": "5",
    "worstRating": "1"
  }
  @endif
}
</script>
@endpush

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Breadcrumb SEO --}}
    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('workers.index') }}" class="hover:text-brand-600">Directorio</a>
        @if($primaryCat)
            <span>›</span>
            <a href="{{ route('category.landing.town', [$primaryCat->slug, \Illuminate\Support\Str::slug($worker->town)]) }}"
               class="hover:text-brand-600">{{ $primaryCat->name }} en {{ $worker->town }}</a>
        @endif
        <span>›</span>
        <span class="text-gray-600">{{ $worker->name }}</span>
    </nav>

    {{-- Perfil principal --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start">
            <img src="{{ $worker->photo_url }}"
                 alt="{{ $worker->name }}"
                 class="w-24 h-24 rounded-full object-cover bg-gray-100 shrink-0">
            <div class="flex-1 min-w-0 text-center sm:text-left">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $worker->name }}</h1>
                <div class="flex items-center gap-2 mt-1 flex-wrap justify-center sm:justify-start">
                    @foreach($worker->categories as $cat)
                        <span class="{{ $loop->first ? 'text-brand-600 font-medium' : 'text-gray-500' }} text-sm">
                            {{ $cat->icon ?? '' }} {{ $cat->name }}
                        </span>
                        @if(!$loop->last)<span class="text-gray-300">·</span>@endif
                    @endforeach
                    <span class="text-gray-300">·</span>
                    <span class="text-gray-500 text-sm">📍 {{ $worker->town }}</span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                    @php $avInfo = $worker->availability_info; @endphp
                    <span class="text-sm font-medium
                        {{ $worker->availability === 'available'   ? 'text-green-600' : '' }}
                        {{ $worker->availability === 'on_request'  ? 'text-yellow-600' : '' }}
                        {{ $worker->availability === 'unavailable' ? 'text-red-500' : '' }}">
                        {{ $avInfo['icon'] }} {{ $avInfo['label'] }}
                    </span>
                    @if($worker->ratings_count > 0)
                        <span class="text-gray-300 hidden sm:inline">·</span>
                        <span class="text-sm font-medium text-green-700">
                            👍 {{ $worker->recommendations_count }}/{{ $worker->ratings_count }} recomiendan
                        </span>
                    @endif
                    @if($worker->years_experience)
                        <span class="text-gray-300 hidden sm:inline">·</span>
                        <span class="text-sm text-gray-500">
                            🗓️ {{ $worker->years_experience }} {{ $worker->years_experience === 1 ? 'año' : 'años' }} de exp.
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($worker->description)
            <p class="mt-4 text-gray-600 leading-relaxed">{{ $worker->description }}</p>
        @endif
        @if($worker->rate_info)
            <div class="mt-3 inline-flex items-center gap-1.5 bg-green-50 border border-green-200 rounded-full px-3 py-1 text-sm text-green-700 font-medium">
                💰 {{ $worker->rate_info }}
            </div>
        @endif

        @if($worker->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach($worker->tags as $tag)
                    <span class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full bg-gray-50 border border-gray-200 text-gray-600">
                        {{ $tag->icon }} {{ $tag->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-5 flex flex-col sm:flex-row gap-3">
            {{-- WhatsApp con tracking --}}
            <a href="{{ $worker->whatsapp_track_url }}"
               class="inline-flex items-center justify-center gap-1.5 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-medium rounded-full transition-colors text-base px-6 py-3 w-full sm:w-auto">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Contactar por WhatsApp
            </a>

            <div class="flex gap-3">
                {{-- Compartir con tracking --}}
                <a href="https://wa.me/?text={{ urlencode('Te comparto el perfil de ' . $worker->name . ' (' . $categoryNames . ') en ServiPueblo: ' . route('workers.show', $worker->slug)) }}"
                   target="_blank" rel="noopener"
                   onclick="trackShare(); return true;"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-green-50 hover:bg-green-100 border border-green-300 text-green-700 text-sm font-medium px-4 py-2.5 rounded-full transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                    </svg>
                    Compartir
                </a>

                {{-- QR --}}
                <button onclick="document.getElementById('qr-section').classList.toggle('hidden')"
                        class="inline-flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-300 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-full transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    QR
                </button>
            </div>
        </div>

        {{-- Sección QR --}}
        <div id="qr-section" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-xs text-gray-500 mb-3 text-center">Escaneá para ver este perfil</p>
            <div id="qrcode" class="flex justify-center"></div>
            <button onclick="downloadQR()"
                    class="mt-3 w-full text-xs text-center text-brand-600 hover:underline">
                Descargar QR
            </button>
        </div>
    </div>

    {{-- Novedades / Muro --}}
    @if($worker->posts->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">📢 Novedades</h2>
        <div class="space-y-4">
            @foreach($worker->posts->take(5) as $post)
            <div class="border-b border-gray-50 last:border-0 pb-4 last:pb-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                    @if($post->is_sold_out)
                        <span class="text-xs font-bold bg-red-100 text-red-600 rounded-full px-2 py-0.5">⚠ AGOTADO</span>
                    @endif
                </div>
                <p class="text-sm text-gray-800 whitespace-pre-wrap {{ $post->is_sold_out ? 'opacity-50 line-through' : '' }}">{{ $post->content }}</p>
                @if($post->photo_url)
                    <img src="{{ $post->photo_url }}" alt="Novedad"
                         class="mt-2 rounded-lg max-h-56 object-cover w-full {{ $post->is_sold_out ? 'opacity-40' : '' }}">
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Horarios de atención --}}
    @if($worker->businessHours->isNotEmpty() && $worker->businessHours->where('is_closed', false)->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">🕐 Horarios de atención</h2>
        <div class="grid grid-cols-1 gap-1 text-sm">
            @foreach($worker->businessHours as $hour)
            <div class="flex justify-between py-1.5 border-b border-gray-50 last:border-0">
                <span class="font-medium text-gray-700">{{ \App\Models\BusinessHour::$days[$hour->day_of_week] }}</span>
                <span class="{{ $hour->is_closed ? 'text-red-400' : 'text-gray-600' }}">
                    {{ $hour->label }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Galería de fotos de trabajos --}}
    @if($worker->photos->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">Trabajos realizados</h2>
        <div class="grid grid-cols-2 gap-2 {{ $worker->photos->count() > 2 ? 'sm:grid-cols-4' : '' }}">
            @foreach($worker->photos as $photo)
                <a href="{{ $photo->url }}" target="_blank" rel="noopener">
                    <img src="{{ $photo->url }}"
                         class="w-full h-32 object-cover rounded-lg border border-gray-100 hover:opacity-90 transition-opacity">
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Calificaciones existentes --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">
            Calificaciones
            @if($worker->ratings_count > 0)
                <span class="text-sm font-normal text-gray-400">({{ $worker->ratings_count }})</span>
            @endif
        </h2>

        @forelse($worker->ratings as $rating)
            <div class="border-b border-gray-50 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-sm text-gray-700">
                            {{ $rating->reviewer_name ?: 'Anónimo' }}
                        </span>
                        <x-star-rating :score="$rating->score" :count="0" />
                    </div>
                    <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                </div>
                @if($rating->comment)
                    <p class="text-sm text-gray-500 mt-1">{{ $rating->comment }}</p>
                @endif
            </div>
        @empty
            <p class="text-gray-400 text-sm">Aún no hay calificaciones. ¡Sé el primero!</p>
        @endforelse
    </div>

    {{-- Formulario de calificación --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Dejar una calificación</h2>

        @auth

            {{-- Usuario logueado --}}
            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded px-3 py-2 text-sm mb-4">
                <div class="flex items-center gap-2">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" class="w-6 h-6 rounded-full">
                    @endif
                    <span class="text-green-700">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-red-500">Cerrar sesión</button>
                </form>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 text-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('ratings.store', $worker) }}" id="rating-form">
                @csrf
                <input type="hidden" name="score" id="score-input" value="{{ old('score') }}">

                {{-- NPS binario --}}
                <div class="mb-5">
                    <p class="text-sm font-medium text-gray-700 mb-3">¿Lo recomendarías? *</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="selectScore(5)"
                                id="btn-yes"
                                class="nps-btn flex-1 flex flex-col items-center gap-1 py-4 rounded-xl border-2 transition-all
                                       {{ old('score') == 5 ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500 hover:border-green-300' }}">
                            <span class="text-3xl">👍</span>
                            <span class="text-sm font-medium">Sí, lo recomiendo</span>
                        </button>
                        <button type="button" onclick="selectScore(1)"
                                id="btn-no"
                                class="nps-btn flex-1 flex flex-col items-center gap-1 py-4 rounded-xl border-2 transition-all
                                       {{ old('score') == 1 ? 'border-red-400 bg-red-50 text-red-600' : 'border-gray-200 text-gray-500 hover:border-red-300' }}">
                            <span class="text-3xl">👎</span>
                            <span class="text-sm font-medium">No lo recomendaría</span>
                        </button>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comentario (opcional)</label>
                    <textarea name="comment" rows="3"
                              placeholder="¿Qué destacarías del servicio?"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('comment') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-3 rounded-lg transition-colors">
                    Enviar calificación
                </button>
            </form>

        @else

            {{-- Usuario no logueado: form anónimo --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 text-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 rounded px-4 py-3 text-sm mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm mb-4">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('ratings.store', $worker) }}">
                @csrf
                <input type="hidden" name="score" id="score-input-anon" value="{{ old('score') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tu nombre</label>
                    <input type="text" name="reviewer_name" value="{{ old('reviewer_name') }}"
                           placeholder="Ej: María García (o dejalo vacío)"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="mb-5">
                    <p class="text-sm font-medium text-gray-700 mb-3">¿Lo recomendarías? *</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="selectScoreAnon(5)"
                                id="btn-yes-anon"
                                class="nps-btn flex-1 flex flex-col items-center gap-1 py-4 rounded-xl border-2 transition-all border-gray-200 text-gray-500 hover:border-green-300">
                            <span class="text-3xl">👍</span>
                            <span class="text-sm font-medium">Sí, lo recomiendo</span>
                        </button>
                        <button type="button" onclick="selectScoreAnon(1)"
                                id="btn-no-anon"
                                class="nps-btn flex-1 flex flex-col items-center gap-1 py-4 rounded-xl border-2 transition-all border-gray-200 text-gray-500 hover:border-red-300">
                            <span class="text-3xl">👎</span>
                            <span class="text-sm font-medium">No lo recomendaría</span>
                        </button>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comentario (opcional)</label>
                    <textarea name="comment" rows="3"
                              placeholder="¿Qué destacarías del servicio?"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('comment') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-3 rounded-lg transition-colors">
                        Enviar calificación
                    </button>
                    <a href="{{ route('auth.google', ['redirect' => route('workers.show', $worker->slug)]) }}"
                       class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Calificar con mi cuenta Google
                    </a>
                </div>
            </form>

        @endauth
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    var qrGenerated = false;
    var qr = null;
    var profileUrl = '{{ route('workers.show', $worker->slug) }}';

    document.getElementById('qr-section').addEventListener('transitionend', generateQR);
    document.querySelector('[onclick*="qr-section"]').addEventListener('click', function() {
        setTimeout(generateQR, 10);
    });

    function generateQR() {
        if (qrGenerated) return;
        qrGenerated = true;
        qr = new QRCode(document.getElementById('qrcode'), {
            text: profileUrl,
            width: 180,
            height: 180,
            colorDark: '#1a1a1a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    function downloadQR() {
        var qrCanvas = document.querySelector('#qrcode canvas');
        if (!qrCanvas) return;

        var padding = 24;
        var qrSize = 200;
        var cardWidth = qrSize + padding * 2;
        var cardHeight = qrSize + 120;

        var canvas = document.createElement('canvas');
        canvas.width = cardWidth;
        canvas.height = cardHeight;
        var ctx = canvas.getContext('2d');

        // Fondo blanco
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, cardWidth, cardHeight);

        // Franja superior verde
        ctx.fillStyle = '#16a34a';
        ctx.fillRect(0, 0, cardWidth, 44);

        // Nombre de la app
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 18px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('ServiPueblo', cardWidth / 2, 28);

        // QR
        ctx.drawImage(qrCanvas, padding, 52, qrSize, qrSize);

        // Nombre trabajador
        ctx.fillStyle = '#111827';
        ctx.font = 'bold 15px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('{{ $worker->name }}', cardWidth / 2, qrSize + 68);

        // Categoría y pueblo
        ctx.fillStyle = '#6b7280';
        ctx.font = '13px sans-serif';
        ctx.fillText('{{ (optional($worker->category)->name ?? $categoryNames) . ' · ' . $worker->town }}', cardWidth / 2, qrSize + 87);

        // URL pequeña
        ctx.fillStyle = '#9ca3af';
        ctx.font = '11px sans-serif';
        ctx.fillText('servipueblo.com', cardWidth / 2, qrSize + 108);

        var link = document.createElement('a');
        link.download = 'qr-{{ $worker->slug }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
</script>
<script>
    function trackShare() {
        fetch('{{ $worker->is_entrepreneur ? route('entrepreneurs.share', $worker->slug) : route('workers.share', $worker->slug) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
    }

    function selectScoreAnon(val) {
        document.getElementById('score-input-anon').value = val;
        var btnYes = document.getElementById('btn-yes-anon');
        var btnNo  = document.getElementById('btn-no-anon');
        if (val === 5) {
            btnYes.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
            btnYes.classList.remove('border-gray-200', 'text-gray-500');
            btnNo.classList.remove('border-red-400', 'bg-red-50', 'text-red-600');
            btnNo.classList.add('border-gray-200', 'text-gray-500');
        } else {
            btnNo.classList.add('border-red-400', 'bg-red-50', 'text-red-600');
            btnNo.classList.remove('border-gray-200', 'text-gray-500');
            btnYes.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
            btnYes.classList.add('border-gray-200', 'text-gray-500');
        }
    }

    function selectScore(val) {
        document.getElementById('score-input').value = val;
        var btnYes = document.getElementById('btn-yes');
        var btnNo  = document.getElementById('btn-no');
        if (val === 5) {
            btnYes.className = btnYes.className.replace(/border-gray-200|border-red-400|bg-red-50|text-red-600|text-gray-500/g, '').trim();
            btnYes.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
            btnNo.className  = btnNo.className.replace(/border-green-500|bg-green-50|text-green-700/g, '').trim();
            btnNo.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
            btnNo.classList.add('border-gray-200', 'text-gray-500');
        } else {
            btnNo.classList.add('border-red-400', 'bg-red-50', 'text-red-600');
            btnNo.classList.remove('border-gray-200', 'text-gray-500');
            btnYes.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
            btnYes.classList.add('border-gray-200', 'text-gray-500');
        }
    }
</script>
@endpush

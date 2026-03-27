@props(['worker', 'showType' => false])

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 flex gap-4 hover:shadow-md transition-shadow">
    {{-- Foto --}}
    <div class="shrink-0">
        <img src="{{ $worker->photo_url }}"
             alt="{{ $worker->name }}"
             class="w-16 h-16 rounded-full object-cover bg-gray-100">
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <h3 class="font-semibold text-gray-900 truncate">{{ $worker->name }}</h3>
            @if($showType)
                <span class="shrink-0 text-xs px-2 py-0.5 rounded-full border
                    {{ $worker->is_entrepreneur
                        ? 'bg-purple-50 text-purple-600 border-purple-200'
                        : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                    {{ $worker->is_entrepreneur ? '🏪 Emprendimiento' : '🔧 Oficio' }}
                </span>
            @endif
        </div>
        <div class="flex flex-wrap gap-1 mt-0.5">
            @foreach($worker->categories as $cat)
                <span class="text-xs {{ $loop->first ? 'text-brand-600 font-medium' : 'text-gray-400' }}">
                    {{ $cat->icon ?? '' }} {{ $cat->name }}{{ !$loop->last ? ' ·' : '' }}
                </span>
            @endforeach
        </div>
        <div class="mt-1 text-xs text-gray-500 flex flex-wrap gap-x-3">
            @if($worker->ratings_count > 0)
                <span>👍 {{ $worker->recommendations_count }}/{{ $worker->ratings_count }} lo recomiendan</span>
            @else
                <span class="text-gray-300">Sin calificaciones</span>
            @endif
            @if($worker->years_experience && !$worker->is_entrepreneur)
                <span class="text-gray-400">🗓️ {{ $worker->years_experience }} años exp.</span>
            @endif
        </div>

        @if($worker->rate_info)
            <span class="inline-block text-xs text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5 mt-1">
                💰 {{ $worker->rate_info }}
            </span>
        @endif
        @if($worker->description)
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $worker->description }}</p>
        @endif

        @if($worker->relationLoaded('tags') && $worker->tags->isNotEmpty())
            <div class="flex flex-wrap gap-1 mt-2">
                @foreach($worker->tags->take(4) as $tag)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-50 border border-gray-200 text-gray-500">
                        {{ $tag->icon }} {{ $tag->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="flex items-center justify-between mt-3">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">📍 {{ $worker->town }}</span>
                @if($worker->availability !== 'available')
                    @php $avInfo = $worker->availability_info; @endphp
                    <span class="text-xs {{ $worker->availability === 'unavailable' ? 'text-red-500' : 'text-yellow-600' }}">
                        {{ $avInfo['icon'] }}
                    </span>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ $worker->profile_url }}"
                   class="text-xs text-brand-600 hover:underline">Ver perfil</a>
                <x-whatsapp-button :url="$worker->whatsapp_track_url" size="sm" />
            </div>
        </div>
    </div>
</div>

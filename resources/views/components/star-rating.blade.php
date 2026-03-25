@props(['score' => 0, 'count' => 0])

@php $score = (float) $score; @endphp

<div class="flex items-center gap-1" title="{{ number_format($score, 1) }} de 5 ({{ $count }} {{ $count === 1 ? 'calificación' : 'calificaciones' }})">
    @for($i = 1; $i <= 5; $i++)
        @if($i <= floor($score))
            <span class="text-yellow-400 text-sm leading-none">★</span>
        @elseif($i - 0.5 <= $score)
            <span class="text-yellow-300 text-sm leading-none">★</span>
        @else
            <span class="text-gray-300 text-sm leading-none">★</span>
        @endif
    @endfor
    @if($count > 0)
        <span class="text-xs text-gray-400 ml-1">({{ $count }})</span>
    @endif
</div>

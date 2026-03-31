<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Página principal --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Tabs principales --}}
    <url>
        <loc>{{ url('/trabajadores?tipo=worker') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/trabajadores?tipo=entrepreneur') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Registro --}}
    <url>
        <loc>{{ route('workers.apply') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- Categorías --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ route('categories.show', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Landing pages categoría + pueblo --}}
    @foreach($categoryTownPairs as $pair)
    <url>
        <loc>{{ $pair['townSlug']
            ? route('category.landing.town', [$pair['catSlug'], $pair['townSlug']])
            : route('category.landing', $pair['catSlug']) }}</loc>
        <lastmod>{{ $pair['updated']->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach

    {{-- Perfiles de trabajadores y emprendimientos --}}
    @foreach($workers as $worker)
    <url>
        <loc>{{ $worker->is_entrepreneur ? route('entrepreneurs.show', $worker->slug) : route('workers.show', $worker->slug) }}</loc>
        <lastmod>{{ $worker->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

</urlset>

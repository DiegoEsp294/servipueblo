<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;
use Illuminate\Support\Str;

class CategoryLandingController extends Controller
{
    public function show(string $catSlug, string $townSlug = null)
    {
        $category = Category::where('slug', $catSlug)->firstOrFail();

        // Normalizar slug de pueblo → nombre real
        $town = null;
        if ($townSlug) {
            $match = Worker::active()
                ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
                ->get(['town'])
                ->first(fn($w) => Str::slug($w->town) === $townSlug);

            if (!$match) abort(404);
            $town = $match->town;
        }

        $query = Worker::active()
            ->with('categories', 'tags')
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->orderByDesc('average_rating');

        if ($town) {
            $query->where('town', $town);
        }

        $workers = $query->get();

        // Pueblos disponibles para esta categoría (para enlaces cruzados)
        $towns = Worker::active()
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->distinct()->orderBy('town')->pluck('town');

        // Meta SEO
        $titleBase = $town
            ? "{$category->name} en {$town}"
            : "{$category->name} en Los Telares";

        $metaTitle = $titleBase . ' | ServiPueblo';

        $metaDescription = $workers->count() > 0
            ? "Encontrá {$workers->count()} " . Str::lower($category->name) . ($town ? " en {$town}" : '') . ". Contacto directo por WhatsApp. Recomendaciones de vecinos."
            : "Buscás " . Str::lower($category->name) . ($town ? " en {$town}" : '') . "? Encontrá profesionales locales en ServiPueblo.";

        return view('workers.category-landing', compact(
            'category', 'workers', 'towns', 'town', 'townSlug',
            'metaTitle', 'metaDescription', 'titleBase'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $workers    = Worker::active()->orderByDesc('updated_at')->get(['slug', 'type', 'updated_at']);
        $categories = Category::ordered()->get(['id', 'slug', 'updated_at']);

        // Combinaciones categoría + pueblo con al menos 1 trabajador activo
        $categoryTownPairs = collect();
        foreach ($categories as $cat) {
            $towns = Worker::active()
                ->whereHas('categories', fn($q) => $q->where('categories.id', $cat->id))
                ->distinct()->pluck('town');

            foreach ($towns as $town) {
                $categoryTownPairs->push([
                    'catSlug'  => $cat->slug,
                    'townSlug' => Str::slug($town),
                    'updated'  => $cat->updated_at,
                ]);
            }

            // También la página de solo categoría (sin pueblo)
            if ($towns->isNotEmpty()) {
                $categoryTownPairs->push([
                    'catSlug'  => $cat->slug,
                    'townSlug' => null,
                    'updated'  => $cat->updated_at,
                ]);
            }
        }

        $content = view('sitemap', compact('workers', 'categories', 'categoryTownPairs'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}

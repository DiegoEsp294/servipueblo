<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $static = public_path('sitemap.xml');
        $content = file_exists($static) ? file_get_contents($static) : $this->build();

        return response($content, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=86400')   // Cloudflare cachea 24hs
            ->header('CDN-Cache-Control', 'public, max-age=86400')
            ->header('Cloudflare-CDN-Cache-Control', 'public, max-age=86400')
            ->header('Vary', 'Accept-Encoding');
    }

    public function generate()
    {
        $content = $this->build();
        file_put_contents(public_path('sitemap.xml'), $content);
    }

    private function build(): string
    {
        $workers    = Worker::active()->orderByDesc('updated_at')->get(['slug', 'type', 'updated_at']);
        $categories = Category::ordered()->get(['id', 'slug', 'updated_at']);

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

            if ($towns->isNotEmpty()) {
                $categoryTownPairs->push([
                    'catSlug'  => $cat->slug,
                    'townSlug' => null,
                    'updated'  => $cat->updated_at,
                ]);
            }
        }

        return view('sitemap', compact('workers', 'categories', 'categoryTownPairs'))->render();
    }
}

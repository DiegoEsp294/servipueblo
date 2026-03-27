<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;

class SitemapController extends Controller
{
    public function index()
    {
        $workers    = Worker::active()->orderByDesc('updated_at')->get(['slug', 'type', 'updated_at']);
        $categories = Category::ordered()->get(['slug', 'updated_at']);

        $content = view('sitemap', compact('workers', 'categories'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}

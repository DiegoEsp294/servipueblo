<?php

use Illuminate\Support\Facades\Route;

// Sin ningún middleware — crawlers, health checks, archivos estáticos

Route::get('/ping', fn() => response('pong', 200));

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);

Route::get('/robots.txt', fn() => response(
    file_get_contents(public_path('robots.txt')), 200,
    ['Content-Type' => 'text/plain']
));

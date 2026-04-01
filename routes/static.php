<?php

use Illuminate\Support\Facades\Route;

// Rutas sin sesión ni cookies — para que Google las pueda leer sin restricciones

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);

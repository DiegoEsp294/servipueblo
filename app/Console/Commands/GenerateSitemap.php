<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate';
    protected $description = 'Genera el sitemap.xml estático en public/';

    public function handle()
    {
        (new SitemapController)->generate();
        $this->info('Sitemap generado en public/sitemap.xml');
    }
}

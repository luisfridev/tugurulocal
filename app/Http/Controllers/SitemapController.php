<?php

namespace App\Http\Controllers;

use App\Actions\Seo\BuildPublicSitemap;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(BuildPublicSitemap $sitemap): Response
    {
        return response($sitemap->handle()->render())
            ->header('Content-Type', 'application/xml; charset=UTF-8')->header('Cache-Control', 'no-store');
    }
}

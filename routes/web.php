<?php

use App\Http\Controllers\DiscoveryController;
use App\Http\Controllers\ListingContactController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingMediaController;
use App\Http\Controllers\ListingReportController;
use App\Http\Controllers\PublicTaxonomyMediaController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TaxonomyMediaController;
use App\Http\Middleware\EnsureEditorialAdmin;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/playa-del-carmen', 302)->name('bootstrap');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/dashboard/fichas/{listing}/previsualizar', [ListingController::class, 'preview'])
    ->middleware(EnsureEditorialAdmin::class)->name('listings.preview');
Route::get('/dashboard/identidad/{type}/{record}/medios/{media}', TaxonomyMediaController::class)
    ->middleware(EnsureEditorialAdmin::class)->whereNumber(['record', 'media'])->name('taxonomy.media');
Route::get('/@{listing}/medios/{media}', ListingMediaController::class)->whereNumber('media')->name('listings.media');
Route::get('/@{listing}/contacto/{kind}', ListingContactController::class)->middleware('throttle:60,1')->name('listings.contact');
Route::get('/@{listing}/reportar', [ListingReportController::class, 'create'])->name('listings.report');
Route::post('/@{listing}/reportar', [ListingReportController::class, 'store'])->middleware('throttle:listing-reports')->name('listings.report.store');
Route::get('/@{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/identidad/{type}/{record}/medios/{media}', PublicTaxonomyMediaController::class)
    ->whereIn('type', ['cities', 'categories', 'areas'])->whereNumber(['record', 'media'])->name('taxonomy.public-media');
Route::get('/{city}/negocios', [DiscoveryController::class, 'businesses'])->where('city', 'playa-del-carmen')->name('businesses.index');
Route::get('/{city}/zonas/{area}', [DiscoveryController::class, 'area'])->where('city', 'playa-del-carmen')->name('areas.show');
Route::get('/{city}/{category}', [DiscoveryController::class, 'category'])->where('city', 'playa-del-carmen')->name('categories.show');
Route::get('/{city}', [DiscoveryController::class, 'home'])->where('city', 'playa-del-carmen')->name('cities.show');

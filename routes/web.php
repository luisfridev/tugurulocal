<?php

use App\Http\Controllers\ListingContactController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingMediaController;
use App\Http\Controllers\ListingReportController;
use App\Http\Controllers\TaxonomyMediaController;
use App\Http\Middleware\EnsureEditorialAdmin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): View {
    return view('welcome');
})->name('bootstrap');

Route::get('/dashboard/fichas/{listing}/previsualizar', [ListingController::class, 'preview'])
    ->middleware(EnsureEditorialAdmin::class)->name('listings.preview');
Route::get('/dashboard/identidad/{type}/{record}/medios/{media}', TaxonomyMediaController::class)
    ->middleware(EnsureEditorialAdmin::class)->whereNumber(['record', 'media'])->name('taxonomy.media');
Route::get('/@{listing}/medios/{media}', ListingMediaController::class)->whereNumber('media')->name('listings.media');
Route::get('/@{listing}/contacto/{kind}', ListingContactController::class)->middleware('throttle:60,1')->name('listings.contact');
Route::get('/@{listing}/reportar', [ListingReportController::class, 'create'])->name('listings.report');
Route::post('/@{listing}/reportar', [ListingReportController::class, 'store'])->middleware('throttle:listing-reports')->name('listings.report.store');
Route::get('/@{listing}', [ListingController::class, 'show'])->name('listings.show');

<?php

namespace App\Actions\Listings;

use App\Models\Listing;
use App\Models\ListingReport;
use Illuminate\Support\Facades\Route;

class SubmitListingReport
{
    public const FIELDS = ['name' => 'Nombre', 'address' => 'Ubicación', 'opening_hours' => 'Horario', 'contacts' => 'Contactos', 'other' => 'Otro dato'];

    public static function available(): bool
    {
        return app()->environment(['local', 'testing']) || (config('editorial.reports_privacy_approved') && Route::has('privacy'));
    }

    /** @param array{field:string,explanation:string} $data */
    public function handle(Listing $listing, array $data): ListingReport
    {
        return $listing->reports()->create(['field' => $data['field'], 'explanation' => $data['explanation']]);
    }
}

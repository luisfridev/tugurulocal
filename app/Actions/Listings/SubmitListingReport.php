<?php

namespace App\Actions\Listings;

use App\Models\Listing;
use App\Models\ListingReport;

class SubmitListingReport
{
    public const FIELDS = ['name' => 'Nombre', 'address' => 'Ubicación', 'opening_hours' => 'Horario', 'contacts' => 'Contactos', 'other' => 'Otro dato'];

    /** @param array{field:string,explanation:string} $data */
    public function handle(Listing $listing, array $data): ListingReport
    {
        return $listing->reports()->create(['field' => $data['field'], 'explanation' => $data['explanation']]);
    }
}

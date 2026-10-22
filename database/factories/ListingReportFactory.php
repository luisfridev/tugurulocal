<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\ListingReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ListingReport> */
class ListingReportFactory extends Factory
{
    public function definition(): array
    {
        return ['listing_id' => Listing::factory(), 'field' => 'opening_hours', 'explanation' => 'El horario necesita revisión.'];
    }
}

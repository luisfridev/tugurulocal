<?php

namespace Database\Factories;

use App\Models\HomeSelection;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HomeSelection> */
class HomeSelectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory()->published(),
            'city_id' => fn (array $attributes): int => Listing::query()->findOrFail($attributes['listing_id'])->city_id,
            'position' => 1,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<City> */
class CityFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Playa del Carmen', 'slug' => 'playa-del-carmen', 'timezone' => 'America/Cancun', 'is_public' => true];
    }
}

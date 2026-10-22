<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Area> */
class AreaFactory extends Factory
{
    public function definition(): array
    {
        return ['city_id' => City::factory(), 'name' => fake()->unique()->city(), 'slug' => fake()->unique()->slug(2), 'is_active' => true];
    }
}

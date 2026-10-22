<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        City::query()->firstOrCreate(['slug' => 'playa-del-carmen'], ['name' => 'Playa del Carmen', 'timezone' => 'America/Cancun', 'is_public' => true]);
        foreach ([['Dónde comer y beber', 'donde-comer-y-beber', 'business'], ['Qué hacer', 'que-hacer', 'business'], ['Servicios', 'servicios', 'business'], ['Experiencias', 'experiencias', 'business'], ['Lugares públicos', 'lugares-publicos', 'place']] as $order => [$name,$slug,$type]) {
            Category::query()->firstOrCreate(['slug' => $slug], ['name' => $name, 'listing_type' => $type, 'sort_order' => $order, 'is_active' => true]);
        }
    }
}

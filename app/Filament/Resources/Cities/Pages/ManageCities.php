<?php

namespace App\Filament\Resources\Cities\Pages;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Resources\Cities\CityResource;
use App\Models\City;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageCities extends ManageRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), new City, $data))];
    }
}

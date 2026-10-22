<?php

namespace App\Filament\Resources\Areas\Pages;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Resources\Areas\AreaResource;
use App\Models\Area;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageAreas extends ManageRecords
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), new Area, $data))];
    }
}

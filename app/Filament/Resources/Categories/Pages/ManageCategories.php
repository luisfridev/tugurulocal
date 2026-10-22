<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), new Category, $data))];
    }
}

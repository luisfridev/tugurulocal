<?php

namespace App\Filament\Resources\Categories;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Forms\TaxonomyIdentityFields;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'categorias';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'Categorías';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nombre')->required()->maxLength(255), TextInput::make('slug')->label('Slug')->required()->maxLength(180), Select::make('listing_type')->label('Tipo de ficha')->options(['business' => 'Negocio', 'place' => 'Lugar público'])->default('business')->required(), TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0)->required(), Toggle::make('is_active')->label('Activa')->default(true), TaxonomyIdentityFields::make()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('Nombre')->searchable(), TextColumn::make('slug')->label('Slug'), TextColumn::make('listing_type')->label('Tipo')->formatStateUsing(fn (string $state): string => $state === 'business' ? 'Negocio' : 'Lugar público'), TextColumn::make('sort_order')->label('Orden')->sortable(), IconColumn::make('is_active')->label('Activa')->boolean()])->recordActions([
            EditAction::make()->using(fn (Model $record, array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), $record, $data)),
        ])->toolbarActions([])->emptyStateHeading('Sin registros')->emptyStateDescription('Crea un registro para comenzar.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageCategories::route('/')];
    }
}

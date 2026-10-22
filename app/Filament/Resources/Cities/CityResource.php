<?php

namespace App\Filament\Resources\Cities;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Forms\TaxonomyIdentityFields;
use App\Filament\Resources\Cities\Pages\ManageCities;
use App\Models\City;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $slug = 'ciudades';

    protected static ?string $navigationLabel = 'Ciudades';

    protected static ?string $modelLabel = 'ciudad';

    protected static ?string $pluralModelLabel = 'Ciudades';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nombre')->required()->maxLength(255), TextInput::make('slug')->label('Slug')->required()->maxLength(180), TextInput::make('timezone')->label('Zona horaria')->default('America/Cancun')->required(), Toggle::make('is_public')->label('Ciudad pública')->default(false)->helperText('Solo Playa del Carmen puede ser pública en el lanzamiento inicial.'), TaxonomyIdentityFields::make()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('Nombre')->searchable(), TextColumn::make('slug')->label('Slug'), TextColumn::make('timezone')->label('Zona horaria'), IconColumn::make('is_public')->label('Pública')->boolean()])->recordActions([
            EditAction::make()->using(fn (Model $record, array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), $record, $data)),
        ])->toolbarActions([])->emptyStateHeading('Sin registros')->emptyStateDescription('Crea un registro para comenzar.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageCities::route('/')];
    }
}

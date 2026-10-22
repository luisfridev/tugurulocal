<?php

namespace App\Filament\Resources\Areas;

use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Forms\TaxonomyIdentityFields;
use App\Filament\Resources\Areas\Pages\ManageAreas;
use App\Models\Area;
use App\Models\City;
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

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $slug = 'zonas';

    protected static ?string $navigationLabel = 'Zonas';

    protected static ?string $modelLabel = 'zona';

    protected static ?string $pluralModelLabel = 'Zonas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nombre')->required()->maxLength(255), TextInput::make('slug')->label('Slug')->required()->maxLength(180), Select::make('city_id')->label('Ciudad')->options(fn (): array => City::query()->pluck('name', 'id')->all())->required(), Toggle::make('is_active')->label('Activa')->default(true), TaxonomyIdentityFields::make()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('Nombre')->searchable(), TextColumn::make('slug')->label('Slug'), TextColumn::make('city.name')->label('Ciudad'), IconColumn::make('is_active')->label('Activa')->boolean()])->recordActions([
            EditAction::make()->using(fn (Model $record, array $data): Model => app(SaveTaxonomy::class)->handle(auth()->user(), $record, $data)),
        ])->toolbarActions([])->emptyStateHeading('Sin registros')->emptyStateDescription('Crea un registro para comenzar.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageAreas::route('/')];
    }
}

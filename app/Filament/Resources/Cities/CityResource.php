<?php

namespace App\Filament\Resources\Cities;

use App\Actions\Listings\SaveHomeSelection;
use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Forms\TaxonomyIdentityFields;
use App\Filament\Resources\Cities\Pages\ManageCities;
use App\Models\City;
use App\Models\HomeSelection;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

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
            static::homeSelectionAction(),
        ])->toolbarActions([])->emptyStateHeading('Sin registros')->emptyStateDescription('Crea un registro para comenzar.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageCities::route('/')];
    }

    private static function homeSelectionAction(): Action
    {
        return Action::make('homeSelection')
            ->label('Selección de Inicio')
            ->authorize('update')
            ->modalHeading('Selección de Inicio')
            ->modalDescription('Elige hasta seis negocios y ordénalos con los botones Subir y Bajar. Un negocio que deje de ser público se conserva aquí, pero no aparece en Inicio.')
            ->modalSubmitActionLabel('Guardar selección')
            ->fillForm(fn (City $record): array => [
                'selections' => $record->homeSelections()->get()->map(fn (HomeSelection $selection): array => ['listing_id' => $selection->listing_id])->all(),
            ])
            ->schema(function (City $record): array {
                $options = app(SaveHomeSelection::class)->options($record);

                return [
                    Repeater::make('selections')
                        ->label('Negocios en Inicio')
                        ->schema([
                            Select::make('listing_id')->label('Negocio')->options($options)->required()->searchable()->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        ])
                        ->defaultItems(0)
                        ->maxItems(SaveHomeSelection::LIMIT)
                        ->addable($options !== [])
                        ->addActionLabel('Añadir negocio')
                        ->reorderableWithButtons()
                        ->reorderableWithDragAndDrop(false)
                        ->moveUpAction(fn (Action $action): Action => $action->label('Subir negocio')->size(Size::ExtraLarge)->extraAttributes(['style' => 'margin: 0']))
                        ->moveDownAction(fn (Action $action): Action => $action->label('Bajar negocio')->size(Size::ExtraLarge)->extraAttributes(['style' => 'margin: 0']))
                        ->deleteAction(fn (Action $action): Action => $action->label('Retirar de Inicio')->size(Size::ExtraLarge)->extraAttributes(['style' => 'margin: 0']))
                        ->itemLabel(fn (array $state): string => $options[$state['listing_id'] ?? ''] ?? 'Elige un negocio')
                        ->helperText($options === [] ? 'No hay negocios públicos elegibles. Publica una ficha con logo autorizado antes de añadirla.' : 'Los negocios señalados como no visibles no se muestran en Inicio hasta recuperar su elegibilidad.'),
                ];
            })
            ->action(function (array $data, City $record, Schema $schema): void {
                try {
                    app(SaveHomeSelection::class)->handle(auth()->user(), $record, array_column($data['selections'] ?? [], 'listing_id'));
                } catch (ValidationException $exception) {
                    throw ValidationException::withMessages([$schema->getStatePath().'.selections' => collect($exception->errors())->flatten()->all()]);
                }
                Notification::make()->title('Selección de Inicio guardada')->success()->send();
            });
    }
}

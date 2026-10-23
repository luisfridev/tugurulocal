<?php

namespace App\Filament\Resources\Submissions;

use App\Actions\Submissions\ModerateSubmission;
use App\Actions\Submissions\SelectSubmissionCredit;
use App\Filament\Resources\Submissions\Pages\ManageSubmissions;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Submission;
use App\Queries\SubmissionMatches;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $slug = 'recomendaciones';

    protected static ?string $navigationLabel = 'Recomendaciones';

    protected static ?string $modelLabel = 'recomendación';

    protected static ?string $pluralModelLabel = 'Recomendaciones';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'listing', 'moderator']);
    }

    public static function table(Table $table): Table
    {
        $actions = [
            Action::make('details')->label('Detalle privado')->authorize('view')->modalSubmitAction(false)->modalCancelActionLabel('Cerrar')
                ->modalContent(fn (Submission $record): View => view('submissions.admin-detail', ['submission' => $record, 'matches' => app(SubmissionMatches::class)->handle(auth()->user(), $record)])),
            Action::make('approve')->label('Aprobar hacia borrador')->authorize('update')->visible(fn (Submission $record): bool => $record->status === 'pending')
                ->fillForm(fn (Submission $record): array => ['mode' => 'create', 'name' => $record->name, 'slug' => Str::slug($record->name), 'primary_category_id' => $record->category_id, 'address' => $record->address, 'maps_url' => $record->maps_url, 'select_credit' => false])
                ->schema([
                    Select::make('mode')->label('Destino')->options(['create' => 'Crear un borrador con los datos elegidos', 'link' => 'Vincular un borrador existente sin modificarlo'])->required()->live(),
                    Select::make('listing_id')->label('Borrador compatible')->options(fn (Submission $record): array => self::listingOptions($record, ['draft']))->searchable()->required(fn (Get $get): bool => $get('mode') === 'link')->visible(fn (Get $get): bool => $get('mode') === 'link'),
                    TextInput::make('name')->label('Nombre elegido')->required()->maxLength(255)->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    TextInput::make('slug')->label('Slug del nuevo borrador')->required()->maxLength(180)->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    Select::make('primary_category_id')->label('Categoría elegida')->options(fn (Submission $record): array => Category::query()->where('listing_type', $record->type)->where('is_active', true)->pluck('name', 'id')->all())->required()->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    Textarea::make('address')->label('Ubicación elegida')->maxLength(3000)->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    TextInput::make('maps_url')->label('Maps elegido')->url()->maxLength(2048)->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    Textarea::make('summary')->label('Resumen editorial elegido')->helperText('Escribe texto público verificado. El motivo privado y la evidencia no se copian.')->maxLength(1000)->visible(fn (Get $get): bool => $get('mode') === 'create'),
                    Checkbox::make('select_credit')->label('Seleccionar su crédito de Instagram')->helperText('Solo si consta consentimiento y un identificador válido. Sustituye el crédito anterior de esta ficha.')->visible(fn (Submission $record): bool => $record->credit_consent && filled($record->instagram)),
                    Textarea::make('moderation_reason')->label('Motivo privado de aprobación')->required()->maxLength(3000),
                ])->action(fn (Submission $record, array $data) => app(ModerateSubmission::class)->handle(auth()->user(), $record, array_replace($data, ['status' => 'approved']))),
        ];
        foreach (['rejected' => 'Rechazar', 'duplicate' => 'Marcar duplicada', 'spam' => 'Marcar spam'] as $status => $label) {
            $fields = [Textarea::make('moderation_reason')->label('Motivo privado de la decisión')->required()->maxLength(3000)];
            if ($status === 'duplicate') {
                $fields[] = Select::make('listing_id')->label('Ficha compatible de referencia (opcional)')->options(fn (Submission $record): array => self::listingOptions($record, ['draft', 'published']))->searchable()->helperText('Puede ser publicada. No se modificará ni se añadirá crédito.');
            }
            $actions[] = Action::make($status)->label($label)->authorize('update')->visible(fn (Submission $record): bool => $record->status === 'pending')->schema($fields)
                ->action(fn (Submission $record, array $data) => app(ModerateSubmission::class)->handle(auth()->user(), $record, $data + ['status' => $status]));
        }
        foreach ([true => 'Seleccionar crédito', false => 'Retirar su crédito'] as $selected => $label) {
            $actions[] = Action::make($selected ? 'select_credit' : 'remove_credit')->label($label)->authorize('update')->requiresConfirmation()
                ->visible(fn (Submission $record): bool => $record->status === 'approved' && $record->listing !== null && ($selected ? $record->credit_consent && filled($record->instagram) : $record->listing->credit_submission_id === $record->id))
                ->action(fn (Submission $record) => app(SelectSubmissionCredit::class)->handle(auth()->user(), $record, (bool) $selected));
        }

        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->label('Propuesta')->searchable()->wrap(),
            TextColumn::make('type')->label('Tipo')->formatStateUsing(fn (string $state): string => $state === 'place' ? 'Lugar' : 'Negocio'),
            TextColumn::make('category.name')->label('Categoría')->wrap(),
            TextColumn::make('status')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => Submission::STATUSES[$state] ?? $state),
            TextColumn::make('listing.name')->label('Ficha vinculada')->placeholder('Sin ficha')->wrap(),
            TextColumn::make('created_at')->label('Recibida')->dateTime('d/m/Y H:i', timezone: 'America/Cancun'),
        ])->filters([
            SelectFilter::make('status')->label('Estado')->options(Submission::STATUSES),
            SelectFilter::make('type')->label('Tipo')->options(['business' => 'Negocio', 'place' => 'Lugar']),
        ])->recordActions($actions)->toolbarActions([])->recordUrl(null)->emptyStateHeading('Sin recomendaciones para mostrar')->emptyStateDescription('Las propuestas públicas aparecerán aquí pendientes de revisión.');
    }

    /** @param list<string> $statuses @return array<int, string> */
    private static function listingOptions(Submission $record, array $statuses): array
    {
        return Listing::query()->where('city_id', $record->city_id)->where('type', $record->type)->whereIn('status', $statuses)->orderBy('name')->get(['id', 'name', 'status'])->mapWithKeys(fn (Listing $listing): array => [$listing->id => $listing->name.' (#'.$listing->id.', '.$listing->status.')'])->all();
    }

    public static function getPages(): array
    {
        return ['index' => ManageSubmissions::route('/')];
    }
}

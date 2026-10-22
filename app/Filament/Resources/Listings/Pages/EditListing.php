<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Actions\Listings\RestoreListingRevision;
use App\Actions\Listings\SaveListing;
use App\Actions\Listings\TransitionListing;
use App\Filament\Resources\Listings\ListingResource;
use App\Models\ContentRevision;
use App\Models\Listing;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = array_replace($data, $this->record->only(Listing::FIELDS));
        $data['category_ids'] = $this->record->categories()->pluck('categories.id')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return app(SaveListing::class)->handle(auth()->user(), $data, $record);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all());
        }
    }

    protected function getHeaderActions(): array
    {
        $actions = [Action::make('preview')->label('Previsualizar')->authorize('view')->url(fn (): string => route('listings.preview', $this->record))->openUrlInNewTab()];
        foreach (['published' => 'Publicar', 'paused' => 'Despublicar', 'archived' => 'Archivar', 'draft' => 'Recuperar borrador'] as $status => $label) {
            $actions[] = Action::make($status)->label($label)->authorize('update')->requiresConfirmation()->visible(fn (): bool => match ($status) {
                'published' => in_array($this->record->status, ['draft', 'paused'], true), 'paused' => $this->record->status === 'published', 'draft' => $this->record->status === 'archived', 'archived' => $this->record->status !== 'archived'
            })
                ->action(function () use ($status): void {
                    try {
                        $this->record = app(TransitionListing::class)->handle(auth()->user(), $this->record, $status);
                    } catch (ValidationException $exception) {
                        Notification::make()->title('No se pudo cambiar el estado')->body(collect($exception->errors())->flatten()->implode(' '))->danger()->persistent()->send();

                        return;
                    }
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Estado actualizado')->success()->send();
                });
        }
        $actions[] = Action::make('revision')->label('Recuperar versión')->authorize('update')->schema([
            Select::make('revision_id')->label('Versión de contenido')->options(fn (): array => $this->record->revisions()->orderByDesc('id')->get()->mapWithKeys(fn (ContentRevision $revision): array => [$revision->id => '#'.$revision->id.' · '.$revision->created_at->timezone('America/Cancun')->format('d/m/Y H:i').' · '.$revision->action])->all())->required(),
        ])->requiresConfirmation()->modalDescription('Recupera textos y clasificación como borrador. Los archivos actuales se conservan; la auditoría no se sobrescribe.')->action(function (array $data): void {
            $revision = $this->record->revisions()->findOrFail($data['revision_id']);
            $this->record = app(RestoreListingRevision::class)->handle(auth()->user(), $this->record, $revision);
            $this->fillForm();
            Notification::make()->title('Versión recuperada como borrador')->success()->send();
        });

        return $actions;
    }
}

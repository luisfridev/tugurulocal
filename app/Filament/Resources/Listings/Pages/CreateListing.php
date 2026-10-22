<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Actions\Listings\SaveListing;
use App\Filament\Resources\Listings\ListingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(SaveListing::class)->handle(auth()->user(), $data);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all());
        }
    }

    protected function getRedirectUrl(): string
    {
        return ListingResource::getUrl('edit', ['record' => $this->record]);
    }
}

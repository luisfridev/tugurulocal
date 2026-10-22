<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    /** @param array<string, mixed> $data */
    protected function handleRecordUpdate(Model $record, #[SensitiveParameter] array $data): Model
    {
        Gate::authorize('update', $record);

        return parent::handleRecordUpdate($record, $data);
    }
}

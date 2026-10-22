<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

class CityPolicy
{
    private function allowed(User $user): bool
    {
        return $user->is_super_admin && filled($user->getAppAuthenticationSecret());
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, City $record): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, City $record): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, City $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, City $record): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, City $record): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}

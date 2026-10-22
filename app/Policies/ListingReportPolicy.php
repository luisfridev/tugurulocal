<?php

namespace App\Policies;

use App\Models\ListingReport;
use App\Models\User;

class ListingReportPolicy
{
    private function allowed(User $user): bool
    {
        return $user->is_super_admin && filled($user->getAppAuthenticationSecret());
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, ListingReport $record): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ListingReport $record): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, ListingReport $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, ListingReport $record): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, ListingReport $record): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}

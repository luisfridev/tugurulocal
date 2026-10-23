<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    private function allowed(User $user): bool
    {
        return $user->is_super_admin && filled($user->getAppAuthenticationSecret());
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, Submission $record): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, Submission $record): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Submission $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Submission $record): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Submission $record): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}

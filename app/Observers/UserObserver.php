<?php

namespace App\Observers;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\User;

class UserObserver
{
    public function updated(User $user): void
    {
        $fields = array_values(array_intersect(array_keys($user->getChanges()), [
            'name', 'email', 'password', 'is_super_admin',
            'app_authentication_secret', 'app_authentication_recovery_codes',
        ]));

        if ($fields === []) {
            return;
        }

        $action = match (true) {
            $user->wasChanged('app_authentication_secret') => $user->getAppAuthenticationSecret() ? AuditAction::MfaEnabled : AuditAction::MfaDisabled,
            $user->wasChanged('app_authentication_recovery_codes') => AuditAction::RecoveryCodesChanged,
            default => AuditAction::AccountUpdated,
        };

        app(RecordAuditEvent::class)->handle($action, AuditResult::Success, auth()->user(), $user, $fields);
    }
}

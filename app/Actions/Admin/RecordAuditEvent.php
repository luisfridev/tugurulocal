<?php

namespace App\Actions\Admin;

use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\AuditEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RecordAuditEvent
{
    /** @param list<string> $changedFields */
    public function handle(AuditAction $action, AuditResult $result, ?User $actor = null, ?Model $subject = null, array $changedFields = []): AuditEvent
    {
        return AuditEvent::query()->create([
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'result' => $result,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'changed_fields' => array_values(array_intersect($changedFields, [
                'name', 'email', 'password', 'is_super_admin',
                'app_authentication_secret', 'app_authentication_recovery_codes',
            ])),
            'source' => request()->route() ? 'web' : 'console',
            'occurred_at' => now(),
        ]);
    }
}

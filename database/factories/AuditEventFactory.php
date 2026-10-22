<?php

namespace Database\Factories;

use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\AuditEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuditEvent> */
class AuditEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'action' => AuditAction::LoginFailed,
            'result' => AuditResult::Failed,
            'source' => 'web',
            'occurred_at' => now(),
            'changed_fields' => [],
        ];
    }
}

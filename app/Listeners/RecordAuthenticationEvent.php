<?php

namespace App\Listeners;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class RecordAuthenticationEvent
{
    public function __construct(private readonly RecordAuditEvent $audit) {}

    public function login(Login $event): void
    {
        if ($event->user instanceof User) {
            $this->audit->handle(AuditAction::Login, AuditResult::Success, $event->user, $event->user);
        }
    }

    public function logout(Logout $event): void
    {
        if ($event->user instanceof User) {
            $this->audit->handle(AuditAction::Logout, AuditResult::Success, $event->user, $event->user);
        }
    }

    public function failed(Failed $event): void
    {
        $this->audit->handle(AuditAction::LoginFailed, AuditResult::Failed, subject: $event->user instanceof User ? $event->user : null);
    }
}

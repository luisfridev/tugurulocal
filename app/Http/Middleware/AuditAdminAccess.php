<?php

namespace App\Http\Middleware;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use Filament\Http\Middleware\Authenticate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuditAdminAccess extends Authenticate
{
    /** @param array<string> $guards */
    protected function authenticate($request, array $guards): void
    {
        try {
            parent::authenticate($request, $guards);
        } catch (HttpException $exception) {
            if ($exception->getStatusCode() === 403) {
                app(RecordAuditEvent::class)->handle(AuditAction::AccessDenied, AuditResult::Denied, $request->user(), $request->user());
            }

            throw $exception;
        }
    }
}

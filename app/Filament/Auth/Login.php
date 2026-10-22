<?php

namespace App\Filament\Auth;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function mount(): void
    {
        $this->forgetObsoleteIntendedDestination();

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([$this->getEmailFormComponent(), $this->getPasswordFormComponent()]);
    }

    public function authenticate(): ?LoginResponse
    {
        $this->forgetObsoleteIntendedDestination();
        $this->data['remember'] = false;

        try {
            return parent::authenticate();
        } catch (ValidationException $exception) {
            foreach (array_keys($exception->errors()) as $field) {
                if (str_starts_with($field, 'data.multiFactor.')) {
                    app(RecordAuditEvent::class)->handle(AuditAction::MfaFailed, AuditResult::Failed, subject: $this->getUserUndertakingMultiFactorAuthentication());
                    break;
                }
            }

            throw $exception;
        }
    }

    private function forgetObsoleteIntendedDestination(): void
    {
        $intendedUrl = session()->get('url.intended');

        if (! is_string($intendedUrl)) {
            return;
        }

        $path = parse_url($intendedUrl, PHP_URL_PATH);

        if (($path === '/admin') || (is_string($path) && str_starts_with($path, '/admin/'))) {
            session()->forget('url.intended');
        }
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        app(RecordAuditEvent::class)->handle(AuditAction::LoginThrottled, AuditResult::Throttled);

        return parent::getRateLimitedNotification($exception);
    }
}

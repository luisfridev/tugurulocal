<?php

namespace App\Enums;

enum AuditAction: string
{
    case ContentChanged = 'content.changed';
    case TaxonomyChanged = 'taxonomy.changed';
    case HomeSelectionChanged = 'home_selection.changed';
    case ReportResolved = 'report.resolved';

    case AdminBootstrapped = 'admin.bootstrapped';
    case Login = 'auth.login';
    case LoginFailed = 'auth.login_failed';
    case LoginThrottled = 'auth.login_throttled';
    case MfaFailed = 'auth.mfa_failed';
    case Logout = 'auth.logout';
    case AccessDenied = 'auth.access_denied';
    case AccountUpdated = 'account.updated';
    case MfaEnabled = 'account.mfa_enabled';
    case MfaDisabled = 'account.mfa_disabled';
    case RecoveryCodesChanged = 'account.recovery_codes_changed';

    public function label(): string
    {
        return match ($this) {
            self::ContentChanged => 'Ficha modificada',
            self::TaxonomyChanged => 'Ciudad, zona o categoría modificada',
            self::HomeSelectionChanged => 'Selección de Inicio modificada',
            self::ReportResolved => 'Reporte revisado',
            self::AdminBootstrapped => 'Primera cuenta administrativa creada',
            self::Login => 'Inicio de sesión',
            self::LoginFailed => 'Acceso rechazado',
            self::LoginThrottled => 'Límite de intentos alcanzado',
            self::MfaFailed => 'Segundo factor rechazado',
            self::Logout => 'Cierre de sesión',
            self::AccessDenied => 'Permiso denegado',
            self::AccountUpdated => 'Cuenta modificada',
            self::MfaEnabled => 'Segundo factor activado',
            self::MfaDisabled => 'Segundo factor desactivado',
            self::RecoveryCodesChanged => 'Códigos de recuperación modificados',
        };
    }
}

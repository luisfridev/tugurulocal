<?php

namespace App\Enums;

enum AuditResult: string
{
    case Success = 'success';
    case Failed = 'failed';
    case Denied = 'denied';
    case Throttled = 'throttled';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Correcto',
            self::Failed => 'Fallido',
            self::Denied => 'Denegado',
            self::Throttled => 'Limitado',
        };
    }
}

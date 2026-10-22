<?php

namespace App\Filament\Resources\AuditEvents\Pages;

use App\Filament\Resources\AuditEvents\AuditEventResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditEvents extends ManageRecords
{
    protected static string $resource = AuditEventResource::class;

    /** @return array<Action> */
    protected function getHeaderActions(): array
    {
        return [];
    }
}

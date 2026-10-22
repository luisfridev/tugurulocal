<?php

namespace App\Actions\Listings;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\ListingReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ResolveListingReport
{
    public function __construct(private RecordAuditEvent $audit) {}

    public function handle(User $actor, ListingReport $report, string $status): void
    {
        Gate::forUser($actor)->authorize('update', $report);
        if (! in_array($status, ['resolved', 'dismissed'], true)) {
            throw ValidationException::withMessages(['status' => 'Decisión no válida.']);
        }
        DB::transaction(function () use ($actor, $report, $status): void {
            $report = ListingReport::query()->lockForUpdate()->findOrFail($report->id);
            if ($report->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Este reporte ya fue revisado.']);
            }
            $report->status = $status;
            $report->resolved_by = $actor->id;
            $report->resolved_at = now();
            $report->save();
            $this->audit->handle(AuditAction::ReportResolved, AuditResult::Success, $actor, $report, ['status']);
        });
    }
}

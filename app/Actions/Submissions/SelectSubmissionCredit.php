<?php

namespace App\Actions\Submissions;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\City;
use App\Models\Listing;
use App\Models\Submission;
use App\Models\User;
use App\Support\SubmissionIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SelectSubmissionCredit
{
    public function __construct(private RecordAuditEvent $audit) {}

    public function handle(User $actor, Submission $submission, bool $selected): void
    {
        Gate::forUser($actor)->authorize('update', $submission);
        DB::transaction(function () use ($actor, $submission, $selected): void {
            City::query()->orderBy('id')->lockForUpdate()->firstOrFail();
            $submission = Submission::query()->lockForUpdate()->findOrFail($submission->id);
            $listing = Listing::query()->lockForUpdate()->find($submission->listing_id);
            if (! $listing || $submission->status !== 'approved' || $listing->city_id !== $submission->city_id || $listing->type !== $submission->type) {
                throw ValidationException::withMessages(['credit' => 'El crédito requiere una recomendación aprobada y vinculada a una ficha compatible.']);
            }
            if ($selected && (! $submission->credit_consent || ! preg_match(SubmissionIdentity::INSTAGRAM_PATTERN, $submission->instagram ?? ''))) {
                throw ValidationException::withMessages(['credit' => 'El crédito requiere consentimiento e identificador de Instagram válido.']);
            }
            if ($selected || $listing->credit_submission_id === $submission->id) {
                $listing->credit_submission_id = $selected ? $submission->id : null;
                $listing->save();
                $this->audit->handle(AuditAction::SubmissionCreditChanged, AuditResult::Success, $actor, $listing);
            }
        }, 3);
    }
}

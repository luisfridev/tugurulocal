<?php

namespace App\Actions\Submissions;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\InteractionEvent;
use App\Models\Submission;
use App\Support\SubmissionIdentity;
use Illuminate\Support\Facades\DB;

class SubmitSubmission
{
    public function __construct(private RecordAuditEvent $audit) {}

    /** @param array<string, mixed> $data */
    public function handle(array $data): Submission
    {
        return DB::transaction(function () use ($data): Submission {
            $submission = new Submission;
            $submission->fill(array_intersect_key($data, array_flip(Submission::INPUT_FIELDS)));
            $submission->name_key = SubmissionIdentity::text($submission->name);
            $submission->address_key = SubmissionIdentity::text($submission->address);
            $submission->maps_key = SubmissionIdentity::url($submission->maps_url);
            $submission->save();
            InteractionEvent::query()->create(['city_id' => $submission->city_id, 'type' => 'submission.sent', 'occurred_at' => now()]);
            $this->audit->handle(AuditAction::SubmissionSent, AuditResult::Success, subject: $submission);

            return $submission;
        });
    }
}

<?php

namespace App\Actions\Submissions;

use App\Actions\Admin\RecordAuditEvent;
use App\Actions\Listings\SaveListing;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\City;
use App\Models\InteractionEvent;
use App\Models\Listing;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ModerateSubmission
{
    public function __construct(private SaveListing $saveListing, private RecordAuditEvent $audit, private SelectSubmissionCredit $credit) {}

    /** @param array<string, mixed> $data */
    public function handle(User $actor, Submission $submission, array $data): Submission
    {
        Gate::forUser($actor)->authorize('update', $submission);
        $valid = Validator::make($data, [
            'status' => ['required', Rule::in(['approved', 'rejected', 'duplicate', 'spam'])],
            'moderation_reason' => ['required', 'string', 'max:3000'],
            'mode' => ['required_if:status,approved', Rule::in(['create', 'link'])],
            'listing_id' => ['nullable', 'integer'],
            'select_credit' => ['sometimes', 'boolean'],
        ], ['moderation_reason.required' => 'Indica el motivo de la decisión.'])->validate();

        return DB::transaction(function () use ($actor, $submission, $data, $valid): Submission {
            City::query()->orderBy('id')->lockForUpdate()->firstOrFail();
            $submission = Submission::query()->lockForUpdate()->findOrFail($submission->id);
            if ($submission->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Esta recomendación ya fue moderada.']);
            }
            $listing = null;
            if ($valid['status'] === 'approved' && $valid['mode'] === 'create') {
                $listing = $this->saveListing->handle($actor, [
                    'city_id' => $submission->city_id, 'type' => $submission->type,
                    'name' => $data['name'] ?? null, 'slug' => $data['slug'] ?? null,
                    'primary_category_id' => $data['primary_category_id'] ?? null,
                    'category_ids' => [$data['primary_category_id'] ?? null],
                    'address' => $data['address'] ?? null, 'maps_url' => $data['maps_url'] ?? null,
                    'summary' => $data['summary'] ?? null,
                    'operating_status' => 'open', 'document_button_label' => 'Descargar menú o folleto',
                    'content_approved' => false,
                ]);
            } elseif ($valid['status'] === 'approved' || ($valid['status'] === 'duplicate' && ! empty($valid['listing_id']))) {
                $listing = Listing::query()->lockForUpdate()->find($valid['listing_id'] ?? null);
                if (! $listing || $listing->city_id !== $submission->city_id || $listing->type !== $submission->type || ! in_array($listing->status, $valid['status'] === 'approved' ? ['draft'] : ['draft', 'published'], true)) {
                    throw ValidationException::withMessages(['listing_id' => 'Elige una ficha compatible de la misma ciudad; aprobar requiere un borrador.']);
                }
            }
            $submission->forceFill([
                'status' => $valid['status'], 'moderated_by' => $actor->id, 'moderated_at' => now(),
                'moderation_reason' => $valid['moderation_reason'], 'listing_id' => $listing?->id,
            ])->save();
            if ($valid['select_credit'] ?? false) {
                $this->credit->handle($actor, $submission, true);
            }
            $this->audit->handle(AuditAction::SubmissionModerated, AuditResult::Success, $actor, $submission, ['status']);
            InteractionEvent::query()->create(['city_id' => $submission->city_id, 'listing_id' => $listing?->id, 'type' => 'submission.'.$valid['status'], 'occurred_at' => now()]);

            return $submission;
        }, 3);
    }
}

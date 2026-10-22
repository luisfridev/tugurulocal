<?php

namespace App\Actions\Listings;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\Listing;
use App\Models\User;

class RecordContentChange
{
    public function __construct(private RecordAuditEvent $audit) {}

    /** @param list<string> $fields */
    public function handle(Listing $listing, User $actor, string $action, array $fields = []): void
    {
        $snapshot = $listing->only(Listing::FIELDS);
        $snapshot['category_ids'] = $listing->categories()->pluck('categories.id')->all();
        $listing->revisions()->create(['actor_id' => $actor->id, 'action' => $action, 'snapshot' => $snapshot]);
        $this->audit->handle(AuditAction::ContentChanged, AuditResult::Success, $actor, $listing, $fields);
    }
}

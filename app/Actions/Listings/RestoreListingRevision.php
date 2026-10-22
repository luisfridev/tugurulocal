<?php

namespace App\Actions\Listings;

use App\Models\ContentRevision;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RestoreListingRevision
{
    public function __construct(private SaveListing $save, private TransitionListing $transition) {}

    public function handle(User $actor, Listing $listing, ContentRevision $revision): Listing
    {
        Gate::forUser($actor)->authorize('update', $listing);
        abort_unless($revision->listing_id === $listing->id, 404);

        return DB::transaction(function () use ($actor, $listing, $revision): Listing {
            $listing = $this->transition->handle($actor, $listing, 'draft');
            $data = $revision->snapshot;
            $data['slug'] = $listing->slug;
            $data['content_approved'] = false;
            $data['media_approved'] = false;
            $data['logo_approved'] = false;

            return $this->save->handle($actor, $data, $listing);
        });
    }
}

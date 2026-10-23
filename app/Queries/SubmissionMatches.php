<?php

namespace App\Queries;

use App\Models\Listing;
use App\Models\Submission;
use App\Models\User;
use App\Support\SubmissionIdentity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class SubmissionMatches
{
    /** @return array{listings: list<array{id: int, name: string, status: string}>, submissions: list<array{id: int, name: string, status: string}>} */
    public function handle(User $actor, Submission $submission): array
    {
        Gate::forUser($actor)->authorize('view', $submission);
        $listings = [];
        foreach (Listing::query()->where('city_id', $submission->city_id)->select(['id', 'name', 'address', 'maps_url', 'status'])->lazyById(100) as $listing) {
            if (SubmissionIdentity::text($listing->name) === $submission->name_key || (filled($listing->address) && SubmissionIdentity::text($listing->address) === $submission->address_key) || ($submission->maps_key && SubmissionIdentity::url($listing->maps_url) === $submission->maps_key)) {
                $listings[] = $listing->only(['id', 'name', 'status']);
                if (count($listings) === 20) {
                    break;
                }
            }
        }
        $submissions = Submission::query()->where('city_id', $submission->city_id)->whereKeyNot($submission->id)
            ->where(function (Builder $query) use ($submission): void {
                $query->where('name_key', $submission->name_key)->orWhere('address_key', $submission->address_key);
                if ($submission->maps_key) {
                    $query->orWhere('maps_key', $submission->maps_key);
                }
            })->orderByDesc('id')->limit(20)->get(['id', 'name', 'status'])->toArray();

        return compact('listings', 'submissions');
    }
}

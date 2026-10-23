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
        $linkKeys = array_values(array_filter([$submission->maps_key, SubmissionIdentity::url($submission->evidence_url)]));
        $listings = [];
        foreach (Listing::query()->where('city_id', $submission->city_id)->select(['id', 'name', 'address', 'maps_url', 'website_url', 'source_url', 'status'])->lazyById(100) as $listing) {
            $listingLinks = array_filter(array_map(SubmissionIdentity::url(...), [$listing->maps_url, $listing->website_url, $listing->source_url]));
            if (SubmissionIdentity::text($listing->name) === $submission->name_key || (filled($listing->address) && SubmissionIdentity::text($listing->address) === $submission->address_key) || array_intersect($linkKeys, $listingLinks) !== []) {
                $listings[] = $listing->only(['id', 'name', 'status']);
                if (count($listings) === 20) {
                    break;
                }
            }
        }
        $submissions = Submission::query()->where('city_id', $submission->city_id)->whereKeyNot($submission->id)
            ->where(function (Builder $query) use ($submission, $linkKeys): void {
                $query->where('name_key', $submission->name_key)->orWhere('address_key', $submission->address_key);
                if ($linkKeys !== []) {
                    $query->orWhereIn('maps_key', $linkKeys);
                }
            })->orderByDesc('id')->limit(20)->get(['id', 'name', 'status'])->toArray();
        if ($linkKeys !== [] && count($submissions) < 20) {
            $candidates = Submission::query()->where('city_id', $submission->city_id)->whereKeyNot($submission->id)
                ->whereNotIn('id', array_column($submissions, 'id'))->whereNotNull('evidence_url')
                ->select(['id', 'name', 'status', 'evidence_url'])->lazyById(100);
            foreach ($candidates as $candidate) {
                if (in_array(SubmissionIdentity::url($candidate->evidence_url), $linkKeys, true)) {
                    $submissions[] = $candidate->only(['id', 'name', 'status']);
                    if (count($submissions) === 20) {
                        break;
                    }
                }
            }
        }

        return compact('listings', 'submissions');
    }
}

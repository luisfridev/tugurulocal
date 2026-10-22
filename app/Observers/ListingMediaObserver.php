<?php

namespace App\Observers;

use App\Models\Listing;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ListingMediaObserver
{
    public function creating(Media $media): void
    {
        $this->protectPublishedLogo($media);
    }

    public function deleting(Media $media): void
    {
        $this->protectPublishedLogo($media);
    }

    public function updating(Media $media): void
    {
        if ($media->isDirty(['model_type', 'model_id', 'collection_name', 'disk', 'file_name'])) {
            $this->protectPublishedLogo($media, true);
        }
    }

    private function protectPublishedLogo(Media $media, bool $checkOriginal = false): void
    {
        $versions = [[$media->model_type, $media->model_id, $media->collection_name]];
        if ($checkOriginal) {
            $versions[] = [$media->getOriginal('model_type'), $media->getOriginal('model_id'), $media->getOriginal('collection_name')];
        }
        foreach ($versions as [$type, $id, $collection]) {
            if ($type === (new Listing)->getMorphClass() && $collection === 'logo' && Listing::query()->whereKey($id)->where('status', 'published')->exists()) {
                throw ValidationException::withMessages(['logo' => 'Despublica la ficha antes de quitar o sustituir su logo.']);
            }
        }
    }

    public function created(Media $media): void
    {
        $this->resetLogoApproval($media);
    }

    public function deleted(Media $media): void
    {
        $this->resetLogoApproval($media);
    }

    private function resetLogoApproval(Media $media): void
    {
        if ($media->model_type === (new Listing)->getMorphClass() && $media->collection_name === 'logo') {
            Listing::query()->whereKey($media->model_id)->update(['logo_approved' => false]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(self::FIELDS)]
#[Hidden(['private_notes', 'media_permission', 'media_source', 'source_url', 'logo_permission', 'logo_source', 'credit_submission_id'])]
class Listing extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const FIELDS = ['city_id', 'area_id', 'primary_category_id', 'type', 'name', 'slug', 'operating_status', 'summary', 'description', 'reasons', 'address', 'opening_hours', 'maps_url', 'website_url', 'facebook_url', 'instagram_url', 'phone', 'whatsapp', 'google_rating', 'google_review_count', 'google_reviewed_at', 'source_url', 'reviewed_at', 'content_approved', 'media_approved', 'media_reviewed_at', 'image_alt', 'document_button_label', 'seo_title', 'seo_description', 'faqs', 'private_notes', 'logo_approved', 'logo_alt', 'logo_reviewed_at'];

    protected $attributes = ['logo_approved' => false, 'content_approved' => false, 'media_approved' => false, 'status' => 'draft', 'was_published' => false, 'type' => 'business', 'operating_status' => 'open', 'document_button_label' => 'Descargar menú o folleto'];

    protected function casts(): array
    {
        return ['content_approved' => 'boolean', 'media_approved' => 'boolean', 'logo_approved' => 'boolean', 'logo_reviewed_at' => 'date', 'is_demo' => 'boolean', 'was_published' => 'boolean', 'published_at' => 'datetime', 'reviewed_at' => 'date', 'google_reviewed_at' => 'date', 'media_reviewed_at' => 'date', 'google_rating' => 'decimal:1', 'google_review_count' => 'integer', 'faqs' => 'array'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'listing_categories');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ContentRevision::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ListingReport::class);
    }

    public function hasAuthorizedLogo(): bool
    {
        return $this->logo_approved && filled($this->logo_alt) && $this->logo_reviewed_at !== null
            && $this->logo_reviewed_at->toDateString() <= now('America/Cancun')->toDateString();
    }

    public function hasAuthorizedMedia(): bool
    {
        return $this->media_approved && $this->media_reviewed_at !== null
            && $this->media_reviewed_at->toDateString() <= now('America/Cancun')->toDateString();
    }

    public function registerMediaCollections(): void
    {
        foreach (['logo', 'cover', 'gallery'] as $name) {
            $collection = $this->addMediaCollection($name)->useDisk('listing_media')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
            if ($name !== 'gallery') {
                $collection->singleFile();
            }
        }
        $this->addMediaCollection('document')->useDisk('listing_media')->acceptsMimeTypes(['application/pdf'])->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->fit(Fit::Max, 1200, 900)->format('webp')->quality(82)->nonQueued()->performOnCollections('logo', 'cover', 'gallery');
    }
}

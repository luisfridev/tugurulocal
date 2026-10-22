<?php

namespace App\Models;

use App\Models\Concerns\HasTaxonomyIdentity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

#[Hidden(['og_image_source', 'og_image_permission', 'icon_source', 'icon_permission'])]
#[Fillable(['city_id', 'name', 'slug', 'is_active', 'primary_color', 'tagline', 'description', 'og_image_alt', 'og_image_reviewed_at', 'og_image_approved', 'icon_alt', 'icon_reviewed_at', 'icon_approved'])]
class Area extends Model implements HasMedia
{
    use HasFactory, HasTaxonomyIdentity;

    protected function casts(): array
    {
        return ['og_image_approved' => 'boolean', 'icon_approved' => 'boolean', 'og_image_reviewed_at' => 'date', 'icon_reviewed_at' => 'date', 'demo_identity_seeded' => 'boolean', 'is_demo' => 'boolean', 'is_active' => 'boolean'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}

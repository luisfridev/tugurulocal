<?php

namespace App\Models;

use App\Models\Concerns\HasTaxonomyIdentity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;

#[Hidden(['og_image_source', 'og_image_permission', 'icon_source', 'icon_permission'])]
#[Fillable(['name', 'slug', 'description', 'listing_type', 'sort_order', 'is_active', 'primary_color', 'tagline', 'og_image_alt', 'og_image_reviewed_at', 'og_image_approved', 'icon_alt', 'icon_reviewed_at', 'icon_approved'])]
class Category extends Model implements HasMedia
{
    use HasFactory, HasTaxonomyIdentity;

    protected function casts(): array
    {
        return ['og_image_approved' => 'boolean', 'icon_approved' => 'boolean', 'og_image_reviewed_at' => 'date', 'icon_reviewed_at' => 'date', 'demo_identity_seeded' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_categories');
    }
}

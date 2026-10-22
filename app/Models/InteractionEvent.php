<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['listing_id', 'city_id', 'category_id', 'area_id', 'page', 'type', 'occurred_at'])]
class InteractionEvent extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public $timestamps = false;

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}

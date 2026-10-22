<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['city_id', 'listing_id', 'position'])]
class HomeSelection extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['source_path', 'listing_id'])]
class SlugRedirect extends Model
{
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}

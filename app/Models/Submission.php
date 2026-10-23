<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(self::INPUT_FIELDS)]
#[Hidden(['email', 'whatsapp', 'evidence_url', 'reason', 'moderation_reason', 'instagram', 'name_key', 'address_key', 'maps_key'])]
class Submission extends Model
{
    use HasFactory;

    public const INPUT_FIELDS = ['city_id', 'category_id', 'type', 'name', 'address', 'reason', 'maps_url', 'evidence_url', 'email', 'whatsapp', 'instagram', 'credit_consent'];

    public const STATUSES = ['pending' => 'Pendiente', 'approved' => 'Aprobada', 'rejected' => 'Rechazada', 'duplicate' => 'Duplicada', 'spam' => 'Spam'];

    protected $attributes = ['status' => 'pending', 'credit_consent' => false];

    protected function casts(): array
    {
        return ['credit_consent' => 'boolean', 'moderated_at' => 'datetime', 'email' => 'encrypted', 'whatsapp' => 'encrypted', 'evidence_url' => 'encrypted'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}

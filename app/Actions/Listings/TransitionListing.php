<?php

namespace App\Actions\Listings;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransitionListing
{
    public function __construct(private RecordContentChange $record) {}

    public function handle(User $actor, Listing $listing, string $status): Listing
    {
        Gate::forUser($actor)->authorize('update', $listing);
        if (! in_array($status, ['published', 'paused', 'archived', 'draft'], true)) {
            throw ValidationException::withMessages(['status' => 'Estado no válido.']);
        }

        return DB::transaction(function () use ($actor, $listing, $status): Listing {
            City::query()->orderBy('id')->lockForUpdate()->firstOrFail();
            $listing = Listing::query()->lockForUpdate()->findOrFail($listing->id);
            if ($listing->status === 'archived' && $status !== 'draft') {
                throw ValidationException::withMessages(['status' => 'Recupera la ficha como borrador antes de publicarla.']);
            }
            if ($status === 'published') {
                self::validatePublication($listing, $listing->categories()->pluck('categories.id')->all());
                $listing->published_at = now();
                $listing->was_published = true;
            }
            $listing->status = $status;
            $listing->save();
            $this->record->handle($listing, $actor, $status, ['status', 'published_at']);

            return $listing;
        }, 3);
    }

    /** @param list<int> $categoryIds */
    public static function validatePublication(Listing $listing, array $categoryIds): void
    {
        $errors = [];
        if ($listing->type !== 'business') {
            $errors['type'] = 'Los Lugares se publicarán cuando existan vínculos verificados en VS12.';
        }
        foreach (['name', 'summary', 'description', 'source_url', 'reviewed_at'] as $field) {
            if (blank($listing->{$field})) {
                $errors[$field] = 'Completa y verifica este dato antes de publicar.';
            }
        }
        $logo = $listing->getFirstMedia('logo');
        if (! $logo || ! Storage::disk($logo->disk)->exists($logo->getPathRelativeToRoot())) {
            $errors['logo'] = 'Carga y guarda un logo antes de publicar.';
        }
        if (! $listing->hasAuthorizedLogo()) {
            $errors['logo_approved'] = 'Autoriza el logo con texto alternativo y fecha válida antes de publicar.';
        }
        if ($listing->is_demo && ! app()->environment(['local', 'testing'])) {
            $errors['is_demo'] = 'La demostración solo puede publicarse en local/testing.';
        }
        if (! $listing->content_approved) {
            $errors['content_approved'] = 'Aprueba el contenido antes de publicar.';
        }
        if (! City::query()->whereKey($listing->city_id)->where('slug', 'playa-del-carmen')->where('is_public', true)->exists()) {
            $errors['city_id'] = 'La ciudad no está disponible públicamente.';
        }
        if (Category::query()->whereIn('id', $categoryIds)->where(fn (Builder $q): Builder => $q->where('is_active', false)->orWhere('listing_type', '!=', $listing->type))->exists() || ! in_array((int) $listing->primary_category_id, array_map('intval', $categoryIds), true)) {
            $errors['category_ids'] = 'Revisa las categorías asociadas y su estado.';
        }
        if ($listing->media_approved && ! $listing->hasAuthorizedMedia()) {
            $errors['media_reviewed_at'] = 'Registra una fecha de verificación válida antes de autorizar los medios.';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}

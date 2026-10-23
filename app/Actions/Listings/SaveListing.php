<?php

namespace App\Actions\Listings;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\SlugRedirect;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaveListing
{
    public function __construct(private RecordContentChange $record) {}

    /** @param array<string, mixed> $data */
    public function handle(User $actor, array $data, ?Listing $listing = null): Listing
    {
        Gate::forUser($actor)->authorize($listing ? 'update' : 'create', $listing ?? Listing::class);

        return DB::transaction(function () use ($actor, $data, $listing): Listing {
            City::query()->orderBy('id')->lockForUpdate()->firstOrFail();
            $listing = $listing ? Listing::query()->lockForUpdate()->findOrFail($listing->id) : new Listing;
            $rules = [
                'name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:180', Rule::notIn(SaveTaxonomy::RESERVED), Rule::unique('listings')->ignore($listing->id)],
                'city_id' => ['required', Rule::exists('cities', 'id')], 'area_id' => ['nullable', Rule::exists('areas', 'id')],
                'primary_category_id' => ['required', Rule::exists('categories', 'id')], 'category_ids' => ['required', 'array', 'min:1'], 'category_ids.*' => ['integer', Rule::exists('categories', 'id')],
                'type' => ['required', Rule::in(['business', 'place'])], 'operating_status' => ['required', Rule::in(['open', 'temporarily_closed', 'permanently_closed'])],
                'summary' => ['nullable', 'string', 'max:1000'], 'description' => ['nullable', 'string', 'max:30000'], 'reasons' => ['nullable', 'string', 'max:5000'],
                'address' => ['nullable', 'string', 'max:3000'], 'opening_hours' => ['nullable', 'string', 'max:3000'],
                'phone' => ['nullable', 'regex:/^\+?[0-9 ()-]{7,30}$/'], 'whatsapp' => ['nullable', 'regex:/^[1-9][0-9]{7,14}$/'],
                'google_rating' => ['nullable', 'numeric', 'between:1,5'], 'google_review_count' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
                'google_reviewed_at' => ['nullable', 'date', 'before_or_equal:'.now('America/Cancun')->toDateString()], 'reviewed_at' => ['nullable', 'date', 'before_or_equal:'.now('America/Cancun')->toDateString()],
                'logo_approved' => ['boolean'], 'logo_alt' => ['nullable', Rule::requiredIf((bool) ($data['logo_approved'] ?? $listing->logo_approved)), 'string', 'max:255'], 'logo_reviewed_at' => ['nullable', Rule::requiredIf((bool) ($data['logo_approved'] ?? $listing->logo_approved)), 'date', 'before_or_equal:'.now('America/Cancun')->toDateString()],
                'media_reviewed_at' => ['nullable', Rule::requiredIf((bool) ($data['media_approved'] ?? $listing->media_approved)), 'date', 'before_or_equal:'.now('America/Cancun')->toDateString()], 'content_approved' => ['boolean'], 'media_approved' => ['boolean'],
                'image_alt' => ['nullable', 'string', 'max:255'],
                'document_button_label' => ['required', 'string', 'max:100'], 'seo_title' => ['nullable', 'string', 'max:160'], 'seo_description' => ['nullable', 'string', 'max:500'],
                'private_notes' => ['nullable', 'string', 'max:10000'], 'faqs' => ['nullable', 'array', 'max:30'], 'faqs.*.question' => ['required', 'string', 'max:500'], 'faqs.*.answer' => ['required', 'string', 'max:3000'],
            ];
            foreach (['maps_url', 'website_url', 'facebook_url', 'instagram_url', 'source_url'] as $field) {
                $rules[$field] = ['nullable', 'url:http,https', 'max:2048'];
            }
            $valid = Validator::make($data, $rules, ['slug.regex' => 'Usa letras minúsculas, números y guiones en el slug.', 'slug.unique' => 'Este slug ya está en uso.'])->validate();
            if (SlugRedirect::query()->where('source_path', '/@'.$valid['slug'])->exists()) {
                throw ValidationException::withMessages(['slug' => 'Este slug está reservado por una URL anterior y no se puede reutilizar.']);
            }
            $ids = array_values(array_unique(array_map('intval', $valid['category_ids'])));
            if (! in_array((int) $valid['primary_category_id'], $ids, true)) {
                throw ValidationException::withMessages(['category_ids' => 'La categoría principal debe estar asociada.']);
            }
            if (Category::query()->whereIn('id', $ids)->where('listing_type', '!=', $valid['type'])->exists()) {
                throw ValidationException::withMessages(['category_ids' => 'Las categorías no corresponden al tipo de ficha.']);
            }
            if ($valid['type'] === 'place' && (count($ids) !== 1 || ! Category::query()->whereKey($valid['primary_category_id'])->where('slug', 'lugares-publicos')->exists())) {
                throw ValidationException::withMessages(['category_ids' => 'Un Lugar solo puede usar la categoría Lugares públicos.']);
            }
            if (! empty($valid['area_id']) && ! Area::query()->whereKey($valid['area_id'])->where('city_id', $valid['city_id'])->exists()) {
                throw ValidationException::withMessages(['area_id' => 'La zona debe pertenecer a la ciudad de la ficha.']);
            }
            if ($listing->was_published && $listing->slug !== $valid['slug']) {
                SlugRedirect::query()->create(['source_path' => '/@'.$listing->slug, 'listing_id' => $listing->id]);
            }
            if ((isset($valid['google_rating']) || isset($valid['google_review_count'])) && empty($valid['google_reviewed_at'])) {
                throw ValidationException::withMessages(['google_reviewed_at' => 'Indica cuándo verificaste los datos de Google.']);
            }
            $listing->fill(array_intersect_key($valid, array_flip(Listing::FIELDS)));
            if ($listing->status === 'published') {
                TransitionListing::validatePublication($listing, $ids);
            }
            $changed = array_keys($listing->getDirty());
            $listing->save();
            $listing->categories()->sync($ids);
            $this->record->handle($listing, $actor, 'saved', [...$changed, 'category_ids']);

            return $listing;
        }, 3);
    }
}

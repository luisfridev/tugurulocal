<?php

namespace App\Actions\Listings;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use App\Queries\PublicListingQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaveHomeSelection
{
    public const LIMIT = 6;

    public function __construct(private RecordAuditEvent $audit, private PublicListingQuery $publicListings) {}

    /** @param list<int|string> $listingIds */
    public function handle(User $actor, City $city, array $listingIds): City
    {
        Gate::forUser($actor)->authorize('update', $city);

        $data = Validator::make(['selections' => $listingIds], [
            'selections' => ['present', 'array', 'max:'.self::LIMIT],
            'selections.*' => ['required', 'integer', 'min:1', 'distinct'],
        ], [
            'selections.max' => 'Selecciona como máximo seis negocios para Inicio.',
            'selections.*.distinct' => 'Cada negocio solo puede aparecer una vez en Inicio.',
            'selections.*.required' => 'Elige un negocio o retira la fila vacía.',
            'selections.*.integer' => 'Elige un negocio válido.',
            'selections.*.min' => 'Elige un negocio válido.',
        ])->validate();
        $listingIds = array_values(array_map(intval(...), $data['selections']));

        return DB::transaction(function () use ($actor, $city, $listingIds): City {
            $city = City::query()->lockForUpdate()->findOrFail($city->id);
            $selectedIds = $city->homeSelections()->pluck('listing_id')->all();
            $listings = Listing::query()->whereKey($listingIds)->with('media')->lockForUpdate()->get()->keyBy('id');

            foreach ($listingIds as $listingId) {
                $listing = $listings->get($listingId);
                if (! $listing || $listing->city_id !== $city->id || $listing->type !== 'business') {
                    throw ValidationException::withMessages(['selections' => 'Selecciona únicamente negocios de esta ciudad.']);
                }

                if (! in_array($listingId, $selectedIds, true) && ($listing->operating_status === 'permanently_closed' || ! $this->publicListings->contains($listing))) {
                    throw ValidationException::withMessages(['selections' => 'Solo puedes añadir negocios públicos con logo autorizado que no estén cerrados permanentemente.']);
                }
            }

            if ($selectedIds !== $listingIds) {
                $city->homeSelections()->delete();
                foreach ($listingIds as $position => $listingId) {
                    $city->homeSelections()->create(['listing_id' => $listingId, 'position' => $position + 1]);
                }
                $this->audit->handle(AuditAction::HomeSelectionChanged, AuditResult::Success, $actor, $city, ['home_selections']);
            }

            return $city->load('homeSelections.listing');
        });
    }

    /** @return array<int, string> */
    public function options(City $city): array
    {
        $eligible = $this->publicListings->forCity($city)
            ->reject(fn (Listing $listing): bool => $listing->operating_status === 'permanently_closed');
        $options = $eligible->sortBy('name')->pluck('name', 'id')->all();

        foreach ($city->homeSelections()->with('listing')->get() as $selection) {
            if (! array_key_exists($selection->listing_id, $options)) {
                $options[$selection->listing_id] = $selection->listing->name.' (no visible en Inicio)';
            }
        }

        return $options;
    }
}

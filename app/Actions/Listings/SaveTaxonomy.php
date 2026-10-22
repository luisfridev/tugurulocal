<?php

namespace App\Actions\Listings;

use App\Actions\Admin\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\User;
use App\Support\TaxonomyContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaveTaxonomy
{
    public const RESERVED = ['negocios', 'explorar', 'ofertas', 'zonas', 'listas', 'blog', 'premium', 'recomendar', 'dashboard', 'admin', 'aviso-de-privacidad', 'sitemap', 'compras'];

    public function __construct(private RecordAuditEvent $audit) {}

    /** @param array<string,mixed> $data */
    public function handle(User $actor, Model $record, array $data): Model
    {
        Gate::forUser($actor)->authorize($record->exists ? 'update' : 'create', $record->exists ? $record : $record::class);

        return DB::transaction(function () use ($actor, $record, $data): Model {
            if ($record->exists) {
                $record = $record->newQuery()->lockForUpdate()->findOrFail($record->id);
            }
            $unique = Rule::unique($record->getTable(), 'slug')->ignore($record->id);
            if ($record instanceof Area) {
                $unique->where('city_id', $data['city_id'] ?? null);
            }
            $rules = ['name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:180', Rule::notIn(self::RESERVED), $unique]];
            $rules += ['primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'], 'tagline' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:30000']];
            foreach (['og_image', 'icon'] as $collection) {
                $rules += [
                    $collection.'_alt' => ['nullable', Rule::requiredIf((bool) ($data[$collection.'_approved'] ?? $record->{$collection.'_approved'})), 'string', 'max:255'],
                    $collection.'_reviewed_at' => ['nullable', Rule::requiredIf((bool) ($data[$collection.'_approved'] ?? $record->{$collection.'_approved'})), 'date', 'before_or_equal:'.now('America/Cancun')->toDateString()],
                    $collection.'_approved' => ['boolean'],
                ];
            }
            if ($record instanceof City) {
                $rules += ['timezone' => ['required', 'timezone'], 'is_public' => ['required', 'boolean']];
            }
            if ($record instanceof Area) {
                $rules += ['city_id' => ['required', Rule::exists('cities', 'id')], 'is_active' => ['required', 'boolean']];
            }
            if ($record instanceof Category) {
                $rules += ['listing_type' => ['required', Rule::in(['business', 'place'])], 'sort_order' => ['required', 'integer', 'min:0'], 'is_active' => ['required', 'boolean']];
            }
            $data = Validator::make($data, $rules)->validate();
            if ($record instanceof City && $data['is_public'] && $data['slug'] !== 'playa-del-carmen') {
                throw ValidationException::withMessages(['is_public' => 'Solo Playa del Carmen puede ser pública en el lanzamiento inicial.']);
            }
            if ($record->exists && $record->listings()->exists()) {
                $locked = $record instanceof Category ? ['slug', 'listing_type'] : ($record instanceof Area ? ['slug', 'city_id'] : ['slug']);
                foreach ($locked as $field) {
                    if ((string) $record->{$field} !== (string) ($data[$field] ?? '')) {
                        throw ValidationException::withMessages([$field => 'No se puede cambiar este campo mientras existan fichas asociadas.']);
                    }
                }
            }
            if (array_key_exists('description', $data)) {
                $data['description'] = TaxonomyContent::sanitize($data['description']);
            }
            $record->fill($data);
            $fields = array_keys($record->getDirty());
            $record->save();
            $this->audit->handle(AuditAction::TaxonomyChanged, AuditResult::Success, $actor, $record, $fields);

            return $record;
        });
    }
}

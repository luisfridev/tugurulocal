<?php

namespace Database\Seeders;

use App\Actions\Listings\TransitionListing;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Support\DemoTaxonomyIdentity;
use App\Support\IdentityColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;
use Spatie\MediaLibrary\HasMedia;

class DemoListingSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Los datos DEMO solo se permiten en local/testing.');
        }
        DB::transaction(function (): void {
            $this->call(DatabaseSeeder::class);
            $city = City::query()->where('slug', 'playa-del-carmen')->firstOrFail();
            $this->identity($city, '#2C254E', DemoTaxonomyIdentity::CITY_TAGLINE);
            $areas = [];
            foreach (['demo-zona-centro' => 'DEMO · Centro 🌴', 'demo-zona-creativa' => 'DEMO · Zona creativa 👩🏽‍🎨'] as $slug => $name) {
                $area = Area::query()->firstOrCreate(['city_id' => $city->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
                if ($area->wasRecentlyCreated) {
                    $area->forceFill(['is_demo' => true])->save();
                }
                if (! $area->is_demo) {
                    throw new LogicException('El slug DEMO de zona ya está ocupado por un registro ajeno.');
                }
                $this->identity($area, count($areas) ? '#74DDCF' : '#8D4C94', DemoTaxonomyIdentity::AREA_TAGLINE);
                $areas[] = $area;
            }
            $categories = Category::query()->orderBy('sort_order')->get();
            foreach ($categories as $index => $category) {
                $this->identity($category, ['#F4C95D', '#287F83', '#7652A2', '#C74B67', '#2C254E'][$index % 5], DemoTaxonomyIdentity::CATEGORY_TAGLINE);
            }
            $slugs = ['donde-comer-y-beber', 'que-hacer', 'servicios', 'experiencias'];
            $definitions = [
                ['demo-cafe-menta', 'DEMO · Café Menta ☕'],
                ['demo-taller-creativo', 'DEMO · Taller Creativo 👩🏽‍🎨'],
                ['demo-estudio-local', 'DEMO · Estudio Local 🛠️'],
                ['demo-experiencia-coral', 'DEMO · Experiencia Coral 🌊'],
                ['demo-borrador-privado', 'DEMO · Borrador privado'],
                ['demo-negocio-pausado', 'DEMO · Negocio pausado'],
                ['demo-estudio-papel', 'DEMO · Estudio Papel ✂️'],
                ['demo-recorrido-brisa', 'DEMO · Recorrido Brisa 🚶'],
                ['demo-cocina-nube', 'DEMO · Cocina Nube 🥘'],
                ['demo-taller-color', 'DEMO · Taller Color 🎨'],
                ['demo-bicicletas-rueda', 'DEMO · Bicicletas Rueda 🚲'],
                ['demo-paseo-luna', 'DEMO · Paseo Luna 🌙'],
                ['demo-panaderia-miga', 'DEMO · Panadería Miga 🥖'],
                ['demo-espacio-movimiento', 'DEMO · Espacio Movimiento 🧘'],
                ['demo-diseno-trazo', 'DEMO · Diseño Trazo 🖍️'],
                ['demo-ruta-semilla', 'DEMO · Ruta Semilla 🌱'],
                ['demo-heladeria-copo', 'DEMO · Heladería Copo 🍧'],
                ['demo-taller-barro', 'DEMO · Taller Barro 🏺'],
                ['demo-fotografia-enfoque', 'DEMO · Fotografía Enfoque 📷'],
                ['demo-recorrido-amanecer', 'DEMO · Recorrido Amanecer 🌅'],
                ['demo-mesa-canela', 'DEMO · Mesa Canela 🍽️'],
                ['demo-espacio-lectura', 'DEMO · Espacio Lectura 📚'],
                ['demo-reparaciones-punto', 'DEMO · Reparaciones Punto 🔧'],
                ['demo-paseo-horizonte', 'DEMO · Paseo Horizonte 🧭'],
                ['demo-cafe-grano', 'DEMO · Café Grano ☕'],
                ['demo-taller-ritmo', 'DEMO · Taller Ritmo 🥁'],
                ['demo-estudio-hilo', 'DEMO · Estudio Hilo 🧵'],
                ['demo-ruta-jardin', 'DEMO · Ruta Jardín 🌿'],
                ['demo-cocina-oliva', 'DEMO · Cocina Oliva 🫒'],
                ['demo-espacio-juego', 'DEMO · Espacio Juego 🧩'],
                ['demo-estudio-plano', 'DEMO · Estudio Plano 📐'],
                ['demo-paseo-marea', 'DEMO · Paseo Marea 🌊'],
            ];
            foreach ($definitions as $index => [$slug, $name]) {
                $category = $categories->firstWhere('slug', $slugs[$index % 4]);
                if (Listing::query()->where('slug', $slug)->exists()) {
                    continue;
                }
                $categoryIds = [$category->id];
                if ($index >= 6) {
                    $categoryIds[] = $categories->firstWhere('slug', $slugs[($index + 1) % 4])->id;
                }
                $listing = Listing::query()->create([
                    'name' => $name, 'slug' => $slug, 'city_id' => $city->id, 'area_id' => $areas[$index < 6 ? $index % 2 : intdiv($index - 6, 4) % 2]->id,
                    'primary_category_id' => $category->id, 'type' => 'business', 'operating_status' => 'open',
                    'summary' => 'DEMO · Contenido sintético para revisar la ficha local. No representa un negocio real.',
                    'description' => 'Este negocio DEMO permite revisar identidad, medios y privacidad. No ofrece servicios reales y no forma parte del inventario de lanzamiento.',
                    'reasons' => $index % 2 === 0 ? 'DEMO · Ejemplo de contenido editorial para revisar lectura y distribución.' : null,
                    'source_url' => 'https://example.org/demo-sintetica', 'reviewed_at' => today('America/Cancun'),
                    'content_approved' => true, 'document_button_label' => ['Ver menú DEMO', 'Descargar guía DEMO', 'Consultar folleto DEMO'][$index % 3],
                    'faqs' => $index % 2 === 0 ? [['question' => '¿Es un negocio real?', 'answer' => 'No. Es una demostración sintética para revisar el sitio local.']] : null,
                ]);
                $listing->forceFill(['is_demo' => true])->save();
                $listing->categories()->sync($categoryIds);
                $this->image($listing, 'logo', 'DEMO LOGO '.($index + 1), '#74DDCF', true);
                $listing->forceFill(['logo_approved' => true, 'logo_alt' => 'Logo sintético DEMO '.($index + 1), 'logo_reviewed_at' => today('America/Cancun')])->save();
                if ($index % 2 === 0) {
                    $this->image($listing, 'cover', 'DEMO PORTADA '.($index + 1), '#2C254E');
                }
                if ($index === 0 || $index === 3) {
                    $this->image($listing, 'gallery', 'DEMO GALERIA '.($index + 1), '#8D4C94');
                }
                if ($index !== 3) {
                    $listing->addMediaFromString($this->pdf())->usingName('DEMO · Documento sintético')->usingFileName($slug.'-demo.pdf')->withCustomProperties(['is_demo' => true])->toMediaCollection('document');
                }
                $listing->forceFill(['media_approved' => true, 'media_reviewed_at' => today('America/Cancun'), 'image_alt' => 'Ilustración geométrica DEMO'])->save();
                if (! in_array($slug, ['demo-borrador-privado', 'demo-negocio-pausado'], true)) {
                    TransitionListing::validatePublication($listing->fresh(), $categoryIds);
                    $listing->forceFill(['status' => 'published', 'published_at' => now(), 'was_published' => true])->save();
                } elseif ($slug === 'demo-negocio-pausado') {
                    $listing->forceFill(['status' => 'paused'])->save();
                }
            }
        });
    }

    private function identity(Model&HasMedia $record, string $color, string $tagline): void
    {
        if ($record->demo_identity_seeded) {
            return;
        }
        foreach (['primary_color' => $color, 'tagline' => $tagline, 'description' => DemoTaxonomyIdentity::DESCRIPTION] as $field => $value) {
            if (blank($record->{$field})) {
                $record->{$field} = $value;
            }
        }
        foreach (['og_image', 'icon'] as $collection) {
            $metadata = collect(['alt', 'source', 'permission', 'reviewed_at', 'approved'])->contains(fn (string $field): bool => filled($record->{$collection.'_'.$field}) && $record->{$collection.'_'.$field} !== false);
            if (! $record->hasMedia($collection) && ! $metadata) {
                $this->image($record, $collection, 'DEMO '.strtoupper($collection), $color, $collection === 'icon');
                $record->forceFill([$collection.'_alt' => 'Imagen sintética DEMO de '.$record->name, $collection.'_reviewed_at' => today('America/Cancun'), $collection.'_approved' => true]);
            }
        }
        $record->forceFill(['demo_identity_seeded' => true])->save();
    }

    private function image(Model&HasMedia $record, string $collection, string $label, string $hex, bool $transparent = false): void
    {
        $width = $transparent ? 320 : 1200;
        $height = $transparent ? 320 : 630;
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $background = imagecolorallocatealpha($image, 255, 255, 255, $transparent ? 127 : 0);
        imagefill($image, 0, 0, $background);
        $color = imagecolorallocate($image, hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2)));
        imagefilledrectangle($image, 20, 20, $width - 20, $height - 20, $color);
        $text = IdentityColor::foreground($hex) === '#000000' ? 0 : 255;
        $ink = imagecolorallocate($image, $text, $text, $text);
        imagestring($image, 5, 40, (int) ($height / 2), $label, $ink);
        imagestring($image, 3, 40, (int) ($height / 2) + 25, 'SOLO REVISION LOCAL', $ink);
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        $record->addMediaFromString($bytes)->usingName($label)->usingFileName('demo-'.$collection.'-'.$record->getTable().'-'.$record->id.'.png')
            ->withCustomProperties(['is_demo' => true, 'alt' => $label])
            ->toMediaCollection($collection);
    }

    private function pdf(): string
    {
        $stream = 'BT /F1 22 Tf 60 740 Td (DEMO - Documento sintetico) Tj 0 -40 Td /F1 12 Tf (Solo revision local. Sin productos, precios ni negocios reales.) Tj ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
    }
}

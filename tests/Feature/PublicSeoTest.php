<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    public function test_sitemap_contains_unique_real_public_urls_and_no_variants_or_future_modules(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $secondary = Category::factory()->create(['slug' => 'que-hacer']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        $real = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        $real->categories()->attach($secondary);
        $another = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create(['is_demo' => true]);
        Listing::factory()->for($city)->for($category, 'primaryCategory')->create();
        Category::factory()->create(['slug' => 'vacia']);
        $response = $this->get('/sitemap.xml?tracking=privado')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertDontSee('privado');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urls = array_map(strval(...), $xml->xpath('//s:loc'));
        $this->assertEqualsCanonicalizing(array_map(url(...), ['/playa-del-carmen', '/playa-del-carmen/negocios', '/playa-del-carmen/servicios', '/playa-del-carmen/que-hacer', '/playa-del-carmen/zonas/centro', '/@'.$real->slug, '/@'.$another->slug]), $urls);
        $this->assertCount(count(array_unique($urls)), $urls);
        foreach (['lastmod', 'priority', 'changefreq'] as $element) {
            $this->assertCount(0, $xml->xpath('//s:'.$element));
        }
        $this->assertDatabaseCount('interaction_events', 0);
        $this->head('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertContent('');
        $this->assertDatabaseCount('interaction_events', 0);
        $this->assertStringContainsString('Disallow: /', file_get_contents(public_path('robots.txt')));
    }

    public function test_empty_or_demo_only_inventory_has_valid_empty_sitemap_and_noindex_details(): void
    {
        $city = City::factory()->create();
        $this->assertCount(0, simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent())->children());
        $demo = Listing::factory()->for($city)->published()->create(['is_demo' => true]);
        $this->get('/@'.$demo->slug)->assertOk()->assertSee('<meta name="robots" content="noindex, follow">', false);
        $this->get('/playa-del-carmen')->assertOk()->assertSee('noindex');
        $this->assertCount(0, simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent())->children());
    }

    public function test_sitemap_immediately_removes_content_with_revoked_eligibility(): void
    {
        $listing = Listing::factory()->published()->create();
        $this->get('/sitemap.xml')->assertSee(url('/@'.$listing->slug));
        $listing->forceFill(['status' => 'paused'])->save();
        $this->get('/sitemap.xml')->assertDontSee('/@'.$listing->slug)->assertDontSee('/playa-del-carmen');
        $listing->forceFill(['status' => 'published'])->save();
        $listing->primaryCategory->update(['is_active' => false]);
        $this->get('/sitemap.xml')->assertDontSee('/@'.$listing->slug);
        $listing->primaryCategory->update(['is_active' => true]);
        $listing->update(['logo_approved' => false]);
        $this->get('/sitemap.xml')->assertDontSee('/@'.$listing->slug);
        $listing->update(['logo_approved' => true]);
        Storage::disk('listing_media')->delete($listing->getFirstMedia('logo')->getPathRelativeToRoot());
        $this->get('/sitemap.xml')->assertDontSee('/@'.$listing->slug);
    }

    public function test_sitemap_rechecks_city_visibility_and_content_approval_on_every_request(): void
    {
        $listing = Listing::factory()->published()->create();
        $this->get('/sitemap.xml')->assertOk()->assertSee(url('/@'.$listing->slug));

        $listing->city->update(['is_public' => false]);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/@'.$listing->slug)->assertDontSee('/playa-del-carmen');

        $listing->city->update(['is_public' => true]);
        $this->get('/sitemap.xml')->assertOk()->assertSee(url('/@'.$listing->slug));

        $listing->update(['content_approved' => false]);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/@'.$listing->slug)->assertDontSee('/playa-del-carmen');
        $this->assertDatabaseCount('interaction_events', 0);
    }

    public function test_canonical_filters_pagination_and_unknown_parameters_follow_one_contract(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        Listing::factory()->count(13)->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        foreach (['/playa-del-carmen/negocios', '/playa-del-carmen/servicios', '/playa-del-carmen/zonas/centro'] as $path) {
            $this->get($path.'?page=1&tracking=SECRETO')->assertOk()->assertSee('<link rel="canonical" href="'.url($path).'">', false)->assertDontSee('SECRETO')->assertDontSee('noindex');
            $this->get($path.'?page=2&tracking=SECRETO')->assertOk()->assertSee('<link rel="canonical" href="'.url($path).'?page=2">', false)->assertDontSee('SECRETO');
        }
        foreach (['/playa-del-carmen/negocios?categoria=servicios', '/playa-del-carmen/servicios?zona=centro', '/playa-del-carmen/zonas/centro?categoria=servicios'] as $path) {
            $this->get($path.'&page=2')->assertOk()->assertSee('noindex, follow')->assertSee('<link rel="canonical" href="'.url(strtok($path, '?')).'">', false);
        }
    }

    public function test_social_metadata_and_breadcrumb_json_match_the_visible_page_and_escape_content(): void
    {
        $listing = Listing::factory()->published()->create(['name' => 'Nombre <script>alert(1)</script>', 'seo_title' => 'Título editorial', 'seo_description' => 'Descripción editorial', 'faqs' => [['question' => '¿Cuál es el horario?', 'answer' => 'Consulta el horario visible.']], 'private_notes' => 'NO-PUBLICAR']);
        $response = $this->get('/@'.$listing->slug.'?tracking=SECRETO')->assertOk()->assertDontSee('NO-PUBLICAR')->assertDontSee('SECRETO');
        $xpath = $this->xpath($response->getContent());
        foreach (['og:title', 'twitter:title'] as $property) {
            $this->assertSame('Título editorial', $xpath->evaluate('string(//meta[@property="'.$property.'" or @name="'.$property.'"]/@content)'));
        }
        $this->assertSame('Descripción editorial', $xpath->evaluate('string(//meta[@name="twitter:description"]/@content)'));
        $this->assertSame($listing->getFirstMedia('logo')->getUrl(), $xpath->evaluate('string(//meta[@name="twitter:image"]/@content)'));
        $schemas = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $value = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
            $schemas = array_merge($schemas, array_is_list($value) ? $value : [$value]);
        }
        $this->assertSame(['LocalBusiness', 'FAQPage', 'BreadcrumbList'], array_column($schemas, '@type'));
        $crumbs = $schemas[2]['itemListElement'];
        $visible = iterator_to_array($xpath->query('//nav[@aria-label="Ubicación"]//li'));
        $this->assertCount(count($visible), $crumbs);
        foreach ($crumbs as $index => $crumb) {
            $this->assertStringContainsString($crumb['name'], $visible[$index]->textContent);
            $this->assertSame($index + 1, $crumb['position']);
        }
        $this->assertSame(url('/@'.$listing->slug), $crumbs[2]['item']);
        $this->assertSame(0, $xpath->query('//script[not(@type) and contains(text(), "alert(1)")]')->length);
    }

    public function test_private_preview_is_noindex_uncached_and_has_no_structured_data(): void
    {
        $listing = Listing::factory()->create();
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $this->get('/dashboard/fichas/'.$listing->id.'/previsualizar')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Cache-Control', 'no-store, private')->assertSee('noindex, nofollow')->assertDontSee('application/ld+json');
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return new DOMXPath($document);
    }
}

<?php

namespace Tests\Feature;

use App\Support\TaxonomyContent;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TaxonomyLegacyMigrationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_incremental_migration_preserves_legacy_plain_text_as_safe_rich_paragraphs(): void
    {
        $migration = require database_path('migrations/2026_10_22_072241_add_identity_and_demo_fields.php');
        $migration->down();
        $id = DB::table('categories')->insertGetId([
            'name' => 'Categoría 👩🏽‍🎨', 'slug' => 'categoria-legacy', 'listing_type' => 'business', 'sort_order' => 0,
            'is_active' => true, 'description' => "Texto <literal> & \"comillas\"\nsegunda línea\n\nOtro párrafo 👨‍👩‍👧‍👦",
        ]);

        $migration->up();

        $html = DB::table('categories')->where('id', $id)->value('description');
        $this->assertSame("<p>Texto &lt;literal&gt; &amp; &quot;comillas&quot;<br>\nsegunda línea</p><p>Otro párrafo 👨‍👩‍👧‍👦</p>", $html);
        $this->assertStringContainsString('&lt;literal&gt;', TaxonomyContent::sanitize($html));
        $this->assertSame('Categoría 👩🏽‍🎨', DB::table('categories')->where('id', $id)->value('name'));
    }
}

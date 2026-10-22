<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['cities', 'areas', 'categories'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->string('primary_color', 7)->nullable();
                $blueprint->string('tagline')->nullable();
                $blueprint->boolean('demo_identity_seeded')->default(false);
                if ($table !== 'categories') {
                    $blueprint->text('description')->nullable();
                }
                foreach (['og_image', 'icon'] as $collection) {
                    $blueprint->string($collection.'_alt')->nullable();
                    $blueprint->string($collection.'_source', 2048)->nullable();
                    $blueprint->text($collection.'_permission')->nullable();
                    $blueprint->date($collection.'_reviewed_at')->nullable();
                    $blueprint->boolean($collection.'_approved')->default(false);
                }
                if ($table === 'areas') {
                    $blueprint->boolean('is_demo')->default(false)->index();
                }
            });
        }
        DB::table('categories')->whereNotNull('description')->orderBy('id')->each(function (object $category): void {
            $paragraphs = preg_split('/\R{2,}/u', $category->description);
            $html = implode('', array_map(fn (string $text): string => '<p>'.nl2br(htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false).'</p>', $paragraphs));
            DB::table('categories')->where('id', $category->id)->update(['description' => $html]);
        });
        Schema::table('listings', function (Blueprint $table): void {
            $table->boolean('is_demo')->default(false)->index();
            $table->boolean('logo_approved')->default(false);
            $table->string('logo_alt')->nullable();
            $table->string('logo_source', 2048)->nullable();
            $table->text('logo_permission')->nullable();
            $table->date('logo_reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('listings', fn (Blueprint $table) => $table->dropColumn(['is_demo', 'logo_approved', 'logo_alt', 'logo_source', 'logo_permission', 'logo_reviewed_at']));
        foreach (['cities', 'areas', 'categories'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                $columns = ['primary_color', 'tagline', 'demo_identity_seeded'];
                foreach (['og_image', 'icon'] as $collection) {
                    foreach (['alt', 'source', 'permission', 'reviewed_at', 'approved'] as $field) {
                        $columns[] = $collection.'_'.$field;
                    }
                }
                if ($name !== 'categories') {
                    $columns[] = 'description';
                }
                if ($name === 'areas') {
                    $columns[] = 'is_demo';
                }
                $table->dropColumn($columns);
            });
        }
    }
};

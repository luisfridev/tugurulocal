<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('timezone')->default('America/Cancun');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
        Schema::create('areas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->unique(['city_id', 'slug']);
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('listing_type', 20)->default('business');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('primary_category_id')->constrained('categories')->restrictOnDelete();
            $table->string('type', 20)->default('business');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 20)->default('draft');
            $table->string('operating_status', 30)->default('open');
            $table->timestamp('published_at')->nullable();
            $table->boolean('was_published')->default(false);
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->text('reasons')->nullable();
            $table->text('address')->nullable();
            $table->text('opening_hours')->nullable();
            foreach (['maps_url', 'website_url', 'facebook_url', 'instagram_url', 'source_url'] as $field) {
                $table->string($field, 2048)->nullable();
            }
            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->decimal('google_rating', 2, 1)->nullable();
            $table->unsignedInteger('google_review_count')->nullable();
            $table->date('google_reviewed_at')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->boolean('content_approved')->default(false);
            $table->boolean('media_approved')->default(false);
            $table->string('media_source', 2048)->nullable();
            $table->text('media_permission')->nullable();
            $table->date('media_reviewed_at')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('document_button_label', 100)->default('Descargar menú o folleto');
            $table->string('seo_title', 160)->nullable();
            $table->text('seo_description')->nullable();
            $table->json('faqs')->nullable();
            $table->text('private_notes')->nullable();
            $table->index(['city_id', 'status', 'published_at', 'id']);
            $table->index(['primary_category_id', 'status', 'published_at']);
            $table->timestamps();
        });
        Schema::create('listing_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unique(['listing_id', 'category_id']);
        });
        Schema::create('content_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->string('action', 40);
            $table->json('snapshot');
            $table->timestamps();
        });
        Schema::create('listing_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->string('field', 50);
            $table->text('explanation');
            $table->string('status', 20)->default('pending');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->index(['status', 'created_at']);
            $table->timestamps();
        });
        Schema::create('interaction_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->string('type', 40);
            $table->timestamp('occurred_at');
            $table->index(['listing_id', 'type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        foreach (['interaction_events', 'listing_reports', 'content_revisions', 'listing_categories', 'listings', 'categories', 'areas', 'cities'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('type', 20);
            $table->string('name');
            $table->text('address');
            $table->text('reason');
            $table->string('maps_url', 2048)->nullable();
            $table->text('evidence_url')->nullable();
            $table->text('email')->nullable();
            $table->text('whatsapp')->nullable();
            $table->string('instagram', 30)->nullable();
            $table->boolean('credit_consent')->default(false);
            $table->string('name_key', 64)->index();
            $table->string('address_key', 64)->index();
            $table->string('maps_key', 64)->nullable()->index();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->foreignId('listing_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('credit_submission_id')->nullable()->constrained('submissions')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_submission_id');
        });
        Schema::dropIfExists('submissions');
    }
};

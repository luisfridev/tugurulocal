<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaction_events', function (Blueprint $table): void {
            $table->unsignedBigInteger('listing_id')->nullable()->change();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('page')->nullable();
            $table->index(['city_id', 'type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        if (DB::table('interaction_events')->whereNull('listing_id')->exists()) {
            throw new RuntimeException('Conserva los eventos de descubrimiento: aplica una migración correctiva en lugar de revertir.');
        }
        Schema::table('interaction_events', function (Blueprint $table): void {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['area_id']);
            $table->dropIndex(['city_id', 'type', 'occurred_at']);
            $table->dropColumn(['city_id', 'category_id', 'area_id', 'page']);
            $table->unsignedBigInteger('listing_id')->nullable(false)->change();
        });
    }
};

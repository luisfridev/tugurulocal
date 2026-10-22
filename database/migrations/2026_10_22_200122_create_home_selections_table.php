<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_selections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('position');
            $table->unique(['city_id', 'listing_id']);
            $table->unique(['city_id', 'position']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_selections');
    }
};

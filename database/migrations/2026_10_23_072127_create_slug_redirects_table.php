<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_path', 182)->unique();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('slug_redirects')->exists()) {
            throw new RuntimeException('No se pueden retirar las reservas de URLs existentes. Usa una migración correctiva.');
        }
        Schema::dropIfExists('slug_redirects');
    }
};

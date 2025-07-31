<?php

declare(strict_types=1);

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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('country_iso_code', 2);
            $table->string('house_number', 20)->nullable();
            $table->string('address_addition')->nullable();
            $table->foreignId('street_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('geo_coordinate_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['street_id', 'house_number']);
            $table->index(['city_id', 'country_iso_code']);
            $table->index('country_iso_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

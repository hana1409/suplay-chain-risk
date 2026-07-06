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
        Schema::create('countries', function (Blueprint $table) {

            $table->id();

            // ===========================
            // COUNTRY IDENTITY
            // ===========================

            // ISO Alpha-2 (ID, US, JP)
            $table->string('country_code', 2)->unique();

            // ISO Alpha-3 (IDN, USA, JPN)
            $table->string('country_code3', 3)->unique();

            // Common Name
            $table->string('country_name');

            // Official Name
            $table->string('official_name')->nullable();

            // ===========================
            // LOCATION
            // ===========================

            $table->string('capital')->nullable();

            $table->string('region')->nullable();

            $table->string('subregion')->nullable();

            $table->decimal('latitude', 10, 6)->nullable();

            $table->decimal('longitude', 10, 6)->nullable();

            // ===========================
            // ECONOMY
            // ===========================

            $table->string('currency_code', 10)->nullable();

            $table->string('currency_name')->nullable();

            // ===========================
            // DEMOGRAPHY
            // ===========================

            $table->string('language')->nullable();

            $table->bigInteger('population')->nullable();

            $table->string('timezone')->nullable();

            // ===========================
            // FLAG
            // ===========================

            // Emoji
            $table->string('flag')->nullable();

            // PNG URL
            $table->string('flag_png')->nullable();

            // SVG URL
            $table->string('flag_svg')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
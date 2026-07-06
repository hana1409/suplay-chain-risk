<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_caches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('rainfall', 5, 2)->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->integer('humidity')->nullable();
            $table->string('weather_condition')->nullable();
            $table->integer('storm_risk')->default(0);

            $table->timestamp('weather_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_caches');
    }
};
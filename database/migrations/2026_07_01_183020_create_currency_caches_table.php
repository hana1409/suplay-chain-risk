<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_caches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('base_currency', 10);
            $table->string('target_currency', 10);

            $table->decimal('exchange_rate', 15, 6);

            $table->timestamp('rate_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_caches');
    }
};
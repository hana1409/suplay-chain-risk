<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_caches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('source')->nullable();

            $table->string('url')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->enum('sentiment', [
                'Positive',
                'Neutral',
                'Negative'
            ])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_caches');
    }
};
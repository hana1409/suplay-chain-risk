<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {

            $table->id();

            $table->string('api_name');

            $table->string('endpoint');

            $table->integer('status_code');

            $table->integer('response_time');

            $table->timestamp('requested_at');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
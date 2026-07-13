<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =============================================
        // 1. Add 'Critical' to risk_scores.risk_level
        // =============================================
        DB::statement("ALTER TABLE risk_scores MODIFY COLUMN risk_level ENUM('Low','Medium','High','Critical') DEFAULT 'Low'");

        // =============================================
        // 2. Add sentiment columns to news_caches
        // =============================================
        Schema::table('news_caches', function (Blueprint $table) {
            if (!Schema::hasColumn('news_caches', 'sentiment')) {
                $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])
                      ->default('Neutral')
                      ->after('source');
            }
            if (!Schema::hasColumn('news_caches', 'sentiment_score')) {
                $table->decimal('sentiment_score', 8, 4)
                      ->default(0)
                      ->after('sentiment');
            }
        });

        // =============================================
        // 3. Add weather_desc/cached_at to weather_caches
        //    (actual columns: temperature, rainfall, wind_speed, humidity, weather_condition, storm_risk, weather_date)
        // =============================================
        Schema::table('weather_caches', function (Blueprint $table) {
            if (!Schema::hasColumn('weather_caches', 'weather_desc')) {
                $table->string('weather_desc')->nullable()->after('weather_condition');
            }
            if (!Schema::hasColumn('weather_caches', 'rain')) {
                $table->decimal('rain', 5, 2)->default(0)->after('rainfall');
            }
            if (!Schema::hasColumn('weather_caches', 'weather_code')) {
                $table->integer('weather_code')->nullable()->after('weather_desc');
            }
        });

        // =============================================
        // 4. Add rate_change_pct to currency_caches
        //    (actual columns: base_currency, target_currency, exchange_rate, rate_date)
        // =============================================
        Schema::table('currency_caches', function (Blueprint $table) {
            if (!Schema::hasColumn('currency_caches', 'rate_change_pct')) {
                $table->decimal('rate_change_pct', 8, 4)->default(0)->after('exchange_rate');
            }
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE risk_scores MODIFY COLUMN risk_level ENUM('Low','Medium','High') DEFAULT 'Low'");

        Schema::table('news_caches', function (Blueprint $table) {
            if (Schema::hasColumn('news_caches', 'sentiment')) {
                $table->dropColumn('sentiment');
            }
            if (Schema::hasColumn('news_caches', 'sentiment_score')) {
                $table->dropColumn('sentiment_score');
            }
        });

        Schema::table('weather_caches', function (Blueprint $table) {
            if (Schema::hasColumn('weather_caches', 'weather_desc')) {
                $table->dropColumn('weather_desc');
            }
            if (Schema::hasColumn('weather_caches', 'rain')) {
                $table->dropColumn('rain');
            }
            if (Schema::hasColumn('weather_caches', 'weather_code')) {
                $table->dropColumn('weather_code');
            }
        });

        Schema::table('currency_caches', function (Blueprint $table) {
            if (Schema::hasColumn('currency_caches', 'rate_change_pct')) {
                $table->dropColumn('rate_change_pct');
            }
        });
    }
};

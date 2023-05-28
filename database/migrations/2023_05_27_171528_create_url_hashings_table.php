<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('url_hashings')) {
            Log::info("url_hashings table already existed in the database");
        } else {
            Log::info("url_hashings table creation process has been started");
            Schema::create('url_hashings', function (Blueprint $table) {
                $table->id();
                $table->text('url', '2048');
                $table->text('hashed_url', '13');
                $table->bigInteger('clicks_count')->null();
                $table->boolean('active');
                $table->timestamps();
            });

            Log::info("url_hashings table has been successfully created");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('url_hashings')) {
            Schema::dropIfExists('url_hashings');
            Log::info("url_hashings table has been successfully droped from the database");
        }
    }
};

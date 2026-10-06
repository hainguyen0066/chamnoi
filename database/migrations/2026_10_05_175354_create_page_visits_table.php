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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('route_name')->nullable();
            $table->unsignedBigInteger('article_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type')->default('desktop'); // mobile, desktop, tablet
            $table->string('referrer_domain')->nullable(); // google.com, facebook.com, zalo.me, direct
            $table->string('user_agent')->nullable();
            $table->date('visited_date');
            $table->timestamps();

            $table->index(['visited_date', 'device_type']);
            $table->index('article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};

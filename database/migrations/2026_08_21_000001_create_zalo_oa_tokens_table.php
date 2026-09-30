<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lưu access_token/refresh_token Zalo OA (1 dòng cho mỗi app_id).
 * expired_at là unix timestamp — so sánh trực tiếp với time().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zalo_oa_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('app_id', 50)->unique();
            $table->text('access_token');
            $table->text('refresh_token');
            $table->unsignedInteger('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zalo_oa_tokens');
    }
};

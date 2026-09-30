<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mã OTP xác thực số điện thoại khi đăng ký. "Dòng mới nhất theo phone" là
 * dòng có hiệu lực. attempts đếm số lần nhập sai để chặn brute-force.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 15)->index();
            $table->string('otp', 6);
            $table->boolean('verified')->default(false);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedInteger('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lộ trình 30 ngày "Cùng con bật âm"
        Schema::create('intervention_days', function (Blueprint $table) {
            $table->id();
            $table->integer('day_number')->unique();
            $table->integer('week_number')->default(1);
            $table->string('title');
            $table->string('goal');
            $table->string('activity_name');
            $table->text('instructions');
            $table->string('target_words')->nullable();
            $table->text('parent_tip');
            $table->string('icon')->default('🌱');
            $table->integer('duration_minutes')->default(20);
            $table->timestamps();
        });

        // 2. Danh bạ cơ sở y tế & can thiệp uy tín
        Schema::create('medical_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city'); // Hà Nội, TP.HCM, Đà Nẵng, Cần Thơ...
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('specialty'); // Tâm bệnh, Thính học, Âm ngữ trị liệu, Phục hồi chức năng
            $table->text('description')->nullable();
            $table->string('booking_tip')->nullable(); // Lưu ý khi đặt lịch khám
            $table->boolean('is_verified')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_centers');
        Schema::dropIfExists('intervention_days');
    }
};

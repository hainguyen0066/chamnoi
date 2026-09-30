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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            // Vị trí hiển thị trên frontend, giá trị hợp lệ khai báo tại Slider::ZONES.
            $table->string('zone', 50)->index();
            $table->string('title')->nullable();
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->string('url')->nullable();
            $table->string('description', 500)->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};

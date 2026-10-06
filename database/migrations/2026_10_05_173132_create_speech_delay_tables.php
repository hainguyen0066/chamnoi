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
        // 1. Bảng bài viết / cẩm nang
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('nguyen-nhan'); // nguyen-nhan, phan-biet, cach-xu-ly, tro-choi, moc-phat-trien, co-do
            $table->string('age_group')->default('all'); // all, 0-12m, 12-24m, 2-3y, 3-5y
            $table->string('reading_time')->default('5 phút đọc');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('icon')->nullable(); // svg hoặc lucide icon name
            $table->string('badge_text')->nullable(); // "Quan trọng", "Nên đọc", "Thực hành ngay"
            $table->string('badge_color')->default('blue'); // blue, rose, amber, emerald, purple
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('order_index')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        // 2. Bảng câu hỏi sàng lọc (Screening Checklist)
        Schema::create('screening_questions', function (Blueprint $table) {
            $table->id();
            $table->string('age_group'); // '12-18m', '18-24m', '2-3y', '3-5y'
            $table->text('question');
            $table->text('explanation')->nullable(); // Lời giải thích trực quan cho phụ huynh
            $table->string('category')->default('ngon_ngu'); // ngon_ngu, giao_tiep_mat, tuong_tac, hanh_vi
            $table->boolean('is_red_flag')->default(false); // Dấu hiệu cảnh báo nguy cơ cao (cờ đỏ)
            $table->integer('points_yes')->default(0);
            $table->integer('points_no')->default(1);
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Bảng kết quả sàng lọc phụ huynh gửi về
        Schema::create('screening_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('child_name')->nullable();
            $table->integer('child_age_months')->default(18);
            $table->string('age_group')->default('18-24m');
            $table->json('answers')->nullable();
            $table->integer('score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('red_flags_count')->default(0);
            $table->string('risk_level')->default('low'); // low, medium, high
            $table->text('advice_summary')->nullable();
            $table->string('status')->default('new'); // new, reviewed, contacted
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 4. Bảng câu hỏi / yêu cầu tư vấn từ phụ huynh
        Schema::create('consultation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('child_age')->nullable();
            $table->string('concern_type')->default('nguyen_nhan'); // nguyen_nhan, phan_biet_tu_ky, can_thiep_tai_nha, kham_chuyen_khoa
            $table->text('message');
            $table->string('status')->default('new'); // new, contacted, resolved
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 5. Bảng cấu hình website (Hotline, Zalo, thông điệp)
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('consultation_requests');
        Schema::dropIfExists('screening_submissions');
        Schema::dropIfExists('screening_questions');
        Schema::dropIfExists('articles');
    }
};

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
        Schema::table('screening_submissions', function (Blueprint $table) {
            $table->string('form_type')->default('general')->after('age_group'); // general, mchat_r, behavior_red_flags, receptive_hearing
            $table->boolean('need_doctor')->default(false)->after('risk_level'); // Khuyến nghị đi khám bác sĩ hay không
            $table->string('clinical_impression')->nullable()->after('need_doctor'); // Dự đoán xu hướng: Chậm nói đơn thuần, Nguy cơ Tự kỷ, v.v.
            $table->json('detected_red_flags')->nullable()->after('clinical_impression'); // Danh sách các cờ đỏ phát hiện
            $table->text('doctor_recommendation')->nullable()->after('detected_red_flags'); // Lời khuyên cụ thể về việc đi khám bác sĩ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screening_submissions', function (Blueprint $table) {
            $table->dropColumn(['form_type', 'need_doctor', 'clinical_impression', 'detected_red_flags', 'doctor_recommendation']);
        });
    }
};

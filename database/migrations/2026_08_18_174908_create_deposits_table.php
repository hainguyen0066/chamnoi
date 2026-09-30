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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('account', 100); // "Tài khoản" gõ tay, giữ nguyên cả khi không khớp user nào
            $table->enum('type', ['xu', 'gold'])->default('xu'); // Loại hình nạp
            $table->enum('method', ['momo', 'bank_transfer', 'ctv', 'vnpay', 'vnptpay']); // Phương thức nạp
            $table->unsignedInteger('amount'); // Số tiền (VND)
            $table->unsignedTinyInteger('promotion_percent')->default(0); // Tỉ lệ khuyến mãi (0/5/10/20)
            $table->unsignedInteger('amount_received'); // Số xu nhận được — LUÔN tính lại phía server
            $table->string('note', 500)->nullable(); // Nội dung nạp (Momo, Paypal, Ck...)
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->enum('source', ['manual', 'gateway'])->default('manual');
            $table->foreignId('processed_by')->nullable()->constrained('users'); // admin thao tác (audit)
            $table->string('gateway_transaction_id', 100)->nullable()->unique(); // mã đơn gửi sang cổng thanh toán
            $table->json('gateway_response')->nullable(); // payload callback thô, phục vụ đối soát
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};

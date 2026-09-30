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
        Schema::create('game_kick_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('account | role');
            $table->string('name', 191)->comment('Tên tài khoản hoặc tên nhân vật');
            $table->string('by', 100)->comment('Tên admin thực hiện kick');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('reason', 255)->nullable()->comment('Lý do kick');
            $table->string('nonce', 64);
            $table->unsignedBigInteger('api_id')->nullable()->comment('ID lệnh từ API GameServer');
            $table->string('code', 10)->nullable()->comment('1: đã kick, 0: pending, 2: lỗi/offline');
            $table->string('result', 50)->nullable()->comment('kicked, pending, not_online, gs_error,...');
            $table->text('msg')->nullable()->comment('Thông điệp trả về');
            $table->integer('gs_id')->nullable()->comment('ID GameServer thực hiện kick');
            $table->string('role_name', 191)->nullable()->comment('Tên nhân vật xác định');
            $table->string('note', 255)->nullable()->comment('Ghi chú từ GameServer');
            $table->string('target', 100)->nullable()->comment('Target tài khoản');
            $table->boolean('reused')->default(false)->comment('Lệnh được gộp/tái sử dụng');
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_kick_logs');
    }
};

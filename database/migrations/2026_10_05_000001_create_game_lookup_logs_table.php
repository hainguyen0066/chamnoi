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
        Schema::create('game_lookup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('by', 20)->comment('role | account');
            $table->string('query_name', 191)->comment('Tên nhân vật hoặc tài khoản cần tra');
            $table->string('admin_name', 100)->nullable()->comment('Admin thực hiện tra cứu');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('code', 10)->nullable()->comment('1: tìm thấy, 2: lỗi hoặc không tìm thấy');
            $table->string('result', 50)->nullable()->comment('ok, not_found, bad_request, bad_sign, forbidden, server_error');
            $table->string('account', 100)->nullable()->comment('Tài khoản tìm được');
            $table->json('roles')->nullable()->comment('Danh sách tên nhân vật tìm được');
            $table->text('msg')->nullable()->comment('Thông điệp từ API');
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('by');
            $table->index('query_name');
            $table->index('account');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_lookup_logs');
    }
};

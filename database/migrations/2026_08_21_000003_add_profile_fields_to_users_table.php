<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung field hồ sơ cho form đăng ký popup: tên đăng nhập, ngày sinh,
 * giới tính (1 nam / 2 nữ), địa chỉ. Nullable vì user cũ (admin) không có.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->date('birthday')->nullable()->after('phone');
            $table->unsignedTinyInteger('gender')->nullable()->after('birthday');
            $table->string('address', 255)->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'birthday', 'gender', 'address']);
        });
    }
};

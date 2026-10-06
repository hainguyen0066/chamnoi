<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name'); // e.g., shopee_affiliate, play_audio, start_test, share_social, spin_wheel
            $table->string('event_label')->nullable(); // e.g., "Sách Ehon", "Gâu gâu", "Test 18m"
            $table->string('page_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->date('event_date');
            $table->timestamps();

            $table->index(['event_date', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};

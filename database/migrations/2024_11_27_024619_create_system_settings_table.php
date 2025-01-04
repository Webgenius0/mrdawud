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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_title', 250)->nullable();
            $table->string('logo', 250)->nullable();
            $table->string('favicon', 250)->nullable();
            $table->string('company_name', 250)->nullable();
            $table->string('tag_line', 250)->nullable();
            $table->string('phone_code', 25)->nullable();
            $table->string('phone_number', 25)->nullable();
            $table->string('whatsapp', 25)->nullable();
            $table->string('email', 250)->nullable();
            $table->foreignId('time_zone')->nullable();
            $table->string('language', 250)->default('en');
            $table->string('country', 250)->nullable();
            $table->string('currency', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

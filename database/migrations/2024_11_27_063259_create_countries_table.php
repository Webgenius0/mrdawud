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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso', 5)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('nicename', 100)->nullable();
            $table->string('iso3', 100)->nullable();
            $table->smallInteger('numcode')->nullable();
            $table->integer('phonecode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

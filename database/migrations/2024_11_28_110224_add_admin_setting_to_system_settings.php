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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('admin_title', 250)->nullable()->after('email');
            $table->string('system_short_name', 100)->nullable()->after('admin_title');
            $table->string('admin_logo', 250)->nullable()->after('system_short_name');
            $table->string('admin_mini_logo', 250)->nullable()->after('admin_logo');
            $table->string('admin_favicon', 250)->nullable()->after('admin_mini_logo');
            $table->string('copyright_text', 500)->nullable()->after('admin_favicon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('admin_title');
            $table->dropColumn('system_short_name');
            $table->dropColumn('admin_logo');
            $table->dropColumn('admin_mini_logo');
            $table->dropColumn('admin_favicon');
            $table->dropColumn('copyright_text');
        });
    }
};

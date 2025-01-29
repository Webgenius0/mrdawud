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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Ensure user_id column exists
           // $table->unsignedBigInteger('product_id');
            $table->string('uuid')->unique();
            //$table->unsignedBigInteger('category_id');
            //$table->unsignedBigInteger('billing_address_id');
            $table->string('payment_method_id');
            $table->enum('status', ['ongoing',  'completed', 'canceled'])->default('ongoing');
            $table->string('product_name')->nullable();
            $table->decimal('sub_total')->nullable();
            $table->decimal('total_price')->nullable();
            //$table->string('title')->nullable();
            $table->integer('quantity')->null;
           // $table->string('email')->nullable();
            $table->decimal('price', 8, 2);
            //$table->decimal('subtotal', 8, 2);
            $table->decimal('tax', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

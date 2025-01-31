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
            //$table->unsignedBigInteger('billing_address_id')->nullable();
           // $table->foreign('billing_address_id')->references('id')->on('billing_addresses')->onDelete('set null');
            $table->string('payment_method_id')->nullable();
            $table->enum('status', ['success','failed','pending','ongoing','completed','canceled'])->default('pending');
            $table->string('product_name')->nullable();
            $table->decimal('sub_total')->nullable();
            $table->decimal('total_price')->nullable();
            //$table->string('title')->nullable();
            $table->integer('quantity')->nullable();
           // $table->string('email')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            //$table->decimal('subtotal', 8, 2);
            $table->decimal('taxes', 8, 2)->nullable();
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

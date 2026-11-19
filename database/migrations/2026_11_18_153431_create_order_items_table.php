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
        Schema::create('order_items', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('discount_amount');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->decimal('tax_amount', 14, 2);
            $table->decimal('unit_price', 14, 2);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

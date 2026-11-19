<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Builder::defaultTimePrecision(null);

        Schema::create('cart_items', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->timestamp('added_at');
            $table->integer('cart_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->foreign('cart_id')->references('id')->on('shopping_carts');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

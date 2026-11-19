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

        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->timestamp('added_at');
            $table->integer('product_id');
            $table->integer('wishlist_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('wishlist_id')->references('id')->on('wishlists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};

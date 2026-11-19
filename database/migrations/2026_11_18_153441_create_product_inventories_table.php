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
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('product_id');
            $table->integer('quantity_on_hand');
            $table->integer('quantity_reserved');
            $table->integer('reorder_level');
            $table->integer('warehouse_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventories');
    }
};

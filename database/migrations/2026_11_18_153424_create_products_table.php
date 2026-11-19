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

        Schema::create('products', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('brand_id');
            $table->integer('category_id');
            $table->decimal('compare_at_price', 14, 2)->nullable();
            $table->timestamp('created_at');
            $table->string('description', 25);
            $table->boolean('is_active');
            $table->string('name', 59);
            $table->decimal('price', 14, 2);
            $table->string('sku', 12)->unique();
            $table->string('slug', 10)->unique();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

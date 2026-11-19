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

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('comment', 35)->nullable();
            $table->timestamp('created_at');
            $table->integer('product_id');
            $table->integer('rating');
            $table->string('title', 21);
            $table->integer('user_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

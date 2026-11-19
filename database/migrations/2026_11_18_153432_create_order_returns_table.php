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

        Schema::create('order_returns', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('order_item_id')->unique();
            $table->timestamp('processed_at');
            $table->integer('quantity');
            $table->integer('reason_id');
            $table->timestamp('requested_at');
            $table->string('status', 9);
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('reason_id')->references('id')->on('return_reasons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};

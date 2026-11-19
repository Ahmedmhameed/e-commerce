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

        Schema::create('payments', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->decimal('amount', 14, 2);
            $table->integer('order_id')->unique();
            $table->integer('payment_method_id');
            $table->timestamp('processed_at');
            $table->string('status', 9);
            $table->string('transaction_reference', 16)->unique();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

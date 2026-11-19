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

        Schema::create('shipments', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->timestamp('delivered_at');
            $table->integer('order_id')->unique();
            $table->timestamp('shipped_at');
            $table->string('tracking_number', 18)->unique();
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

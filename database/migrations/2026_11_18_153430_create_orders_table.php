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

        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('billing_address_id');
            $table->integer('coupon_id')->nullable();
            $table->timestamp('created_at');
            $table->decimal('discount_amount', 14, 2);
            $table->integer('order_status_id');
            $table->integer('shipping_address_id');
            $table->decimal('shipping_cost', 14, 2);
            $table->integer('shipping_method_id');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('tax_amount', 14, 2);
            $table->decimal('total', 14, 2);
            $table->integer('user_id');
            $table->foreign('billing_address_id')->references('id')->on('addresses');
            $table->foreign('coupon_id')->references('id')->on('discount_coupons');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('shipping_address_id')->references('id')->on('addresses');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
            $table->foreign('user_id')->references('id')->on('users');
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

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

        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('code', 9)->unique();
            $table->string('discount_type', 120);
            $table->decimal('discount_value', 14, 2);
            $table->timestamp('ends_at');
            $table->boolean('is_active');
            $table->integer('min_purchase_amount');
            $table->timestamp('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};

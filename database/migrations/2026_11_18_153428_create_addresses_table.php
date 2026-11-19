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
        Schema::create('addresses', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('city', 40);
            $table->integer('country_id');
            $table->string('extended_address', 7)->nullable();
            $table->boolean('is_default_billing');
            $table->boolean('is_default_shipping');
            $table->string('postal_code', 10);
            $table->string('state_province', 3);
            $table->string('street_address', 101);
            $table->integer('user_id');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

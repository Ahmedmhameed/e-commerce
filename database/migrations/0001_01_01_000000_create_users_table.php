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

        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->timestamp('created_at');
            $table->string('email', 78)->unique();
            $table->string('first_name', 30);
            $table->string('last_name', 44);
            $table->string('password_hash', 60);
            $table->string('phone', 26);
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

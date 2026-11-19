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

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->timestamp('created_at');
            $table->string('priority', 6);
            $table->string('status', 8);
            $table->string('subject', 26);
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

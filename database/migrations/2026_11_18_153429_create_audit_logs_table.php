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

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('action', 6);
            $table->timestamp('performed_at');
            $table->integer('record_id');
            $table->string('table_affected', 9);
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

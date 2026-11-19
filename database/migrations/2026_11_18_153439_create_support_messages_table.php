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

        Schema::create('support_messages', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('message', 36);
            $table->string('sender_type', 8);
            $table->timestamp('sent_at');
            $table->integer('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('support_tickets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};

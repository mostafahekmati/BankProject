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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_card_id')->constrained('cards')->onDelete('cascade');
            $table->foreignId('destination_card_id')->constrained('cards')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(500);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
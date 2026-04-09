<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Asegúrate de que diga associate_id (en singular y con guion bajo)
            $table->foreignId('associate_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->json('months_paid');
            $table->decimal('late_fee_applied', 10, 2)->default(0);
            $table->timestamps();
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

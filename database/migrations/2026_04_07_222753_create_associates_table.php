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
        Schema::create('associates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained(); // Relación con sectores
            $table->string('name');
            $table->string('last_name');
            $table->string('dni', 15)->unique();
            $table->string('address');
            $table->date('entry_date'); // Vital para saber desde cuándo debe
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associates');
    }
};

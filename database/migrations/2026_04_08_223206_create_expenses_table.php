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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description'); // Qué se compró
            $table->enum('category', ['Materiales', 'Servicios', 'Planilla', 'Viáticos', 'Otros'])->default('Otros');
            $table->decimal('amount', 10, 2); // Cuánto costó
            $table->date('date'); // Fecha del gasto
            $table->string('voucher_number')->nullable(); // Nro de Boleta o Factura
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }
};

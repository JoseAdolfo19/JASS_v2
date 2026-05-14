<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraordinary_payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Nombre: "Cuota Aniversario", "Cuota Mantenimiento"
            $table->text('description')->nullable();       // Descripción acordada en reunión
            $table->decimal('amount', 10, 2);             // Monto acordado
            $table->date('decided_at')->nullable();        // Fecha de la reunión en que se decidió
            $table->boolean('active')->default(true);      // Si aún está vigente para cobrar
            $table->timestamps();
        });

        // Tabla pivote: qué socios ya pagaron cada cuota extraordinaria
        Schema::create('extraordinary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extraordinary_payment_type_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('associate_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('payment_id')               // Referencia al recibo general
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraordinary_payments');
        Schema::dropIfExists('extraordinary_payment_types');
    }
};
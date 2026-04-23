<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de eventos (asambleas / faenas)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['asamblea', 'faena']);
            $table->string('title');
            $table->date('date');
            $table->text('description')->nullable();
            $table->boolean('lista_cerrada')->default(false); // true = ya no se puede editar
            $table->timestamps();
        });

        // Tabla pivot de asistencia
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('associate_id')->constrained('associates')->cascadeOnDelete();
            $table->enum('status', ['presente', 'ausente', 'justificado'])->default('ausente');
            $table->timestamps();

            $table->unique(['event_id', 'associate_id']); // un registro por socio por evento
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
        Schema::dropIfExists('events');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla connections con todos los campos de una vez
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('associate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->default('Conexión Principal');
            $table->boolean('is_primary')->default(false);
            $table->string('address')->nullable();
            $table->string('meter_number')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. Ahora sí existe connections — agregar FK en payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('connection_id')
                ->nullable()
                ->after('associate_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('connection_id');
        });

        Schema::dropIfExists('connections');
    }
};
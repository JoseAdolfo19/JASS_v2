<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            // Fecha desde la que se cobra esta instalación
            $table->date('entry_date')->nullable()->after('active');
            // Estado independiente de la instalación
            $table->enum('status', ['activo', 'suspendido'])->default('activo')->after('entry_date');
        });
    }

    public function down(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->dropColumn(['entry_date', 'status']);
        });
    }
};
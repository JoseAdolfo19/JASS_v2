<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Verificamos si no existen para evitar errores al migrar
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type')->default('cuota text')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'concept')) {
                $table->text('concept')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'concept']);
        });
    }
};
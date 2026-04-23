<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Tipo de comprobante
            $table->enum('voucher_type', [
                'boleta',
                'factura',
                'recibo_honorarios',
                'declaracion_jurada',
                'otro',
            ])->default('otro')->after('voucher_path');

            // Campos extra compartidos por todos los tipos
            $table->string('beneficiary')->nullable()->after('voucher_type');  // Proveedor / Beneficiario
            $table->string('ruc_dni')->nullable()->after('beneficiary');        // RUC o DNI del emisor
            $table->text('notes')->nullable()->after('ruc_dni');               // Observaciones / detalle libre
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['voucher_type', 'beneficiary', 'ruc_dni', 'notes']);
        });
    }
};
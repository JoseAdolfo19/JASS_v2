<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'category',
        'amount',
        'date',
        'voucher_number',
        'voucher_path',
        'voucher_type',   // boleta | factura | recibo_honorarios | declaracion_jurada | otro
        'beneficiary',    // Proveedor o beneficiario
        'ruc_dni',        // RUC o DNI del emisor
        'notes',          // Observaciones adicionales
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getVoucherTypeLabelAttribute(): string
    {
        return match($this->voucher_type) {
            'boleta'             => 'Boleta de Venta',
            'factura'            => 'Factura',
            'recibo_honorarios'  => 'Recibo por Honorarios',
            'declaracion_jurada' => 'Declaración Jurada',
            default              => 'Otro Comprobante',
        };
    }

    public function getVoucherTypeColorAttribute(): string
    {
        return match($this->voucher_type) {
            'boleta'             => 'blue',
            'factura'            => 'purple',
            'recibo_honorarios'  => 'green',
            'declaracion_jurada' => 'orange',
            default              => 'zinc',
        };
    }
}
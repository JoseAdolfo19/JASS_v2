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
        'voucher_path',      // Ruta de la imagen del comprobante
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraordinaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'extraordinary_payment_type_id',
        'associate_id',
        'payment_id',
        'amount_paid',
    ];

    protected $casts = [
        'amount_paid' => 'float',
    ];

    public function type()
    {
        return $this->belongsTo(ExtraordinaryPaymentType::class, 'extraordinary_payment_type_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
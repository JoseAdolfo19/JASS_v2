<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'associate_id',
        'amount',
        'type',    
        'concept',
        'invoice_number',
        'months_paid',
        'late_fee_applied'
    ];

    protected $casts = [
        'months_paid' => 'array',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }
}
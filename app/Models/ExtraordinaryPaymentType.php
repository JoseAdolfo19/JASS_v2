<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraordinaryPaymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'decided_at',
        'active',
    ];

    protected $casts = [
        'amount'      => 'float',
        'decided_at'  => 'date',
        'active'      => 'boolean',
    ];

    /**
     * Socios que ya pagaron esta cuota extraordinaria.
     */
    public function paidByAssociates()
    {
        return $this->hasMany(ExtraordinaryPayment::class);
    }

    /**
     * Verifica si un socio ya pagó esta cuota.
     */
    public function isPaidBy(int $associateId): bool
    {
        return $this->paidByAssociates()
            ->where('associate_id', $associateId)
            ->exists();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'date', 'fine_amount', 'sector_id'];

    // AGREGAR ESTA RELACIÓN
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
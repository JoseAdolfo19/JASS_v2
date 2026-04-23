<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'type',
        'title',
        'date',
        'description',
        'lista_cerrada',
    ];

    protected $casts = [
        'date'          => 'date',
        'lista_cerrada' => 'boolean',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function associates()
    {
        return $this->belongsToMany(Associate::class, 'event_attendances')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getTotalPresentesAttribute(): int
    {
        return $this->attendances()->where('status', 'presente')->count();
    }

    public function getTotalAusentesAttribute(): int
    {
        return $this->attendances()->where('status', 'ausente')->count();
    }

    public function getTotalJustificadosAttribute(): int
    {
        return $this->attendances()->where('status', 'justificado')->count();
    }
}
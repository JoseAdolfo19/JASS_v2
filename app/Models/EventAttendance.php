<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    protected $fillable = [
        'event_id',
        'associate_id',
        'status', // presente | ausente | justificado
        'fine_paid',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }
}
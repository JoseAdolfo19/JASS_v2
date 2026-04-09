<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sector_id',
        'name',
        'last_name',
        'dni',
        'address',
        'meter_number',      // Número de medidor
        'address_reference', // Manzana / Lote
        'entry_date',
        'status',            // activo | suspendido
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Calcula la deuda actual del socio.
     * Lee la cuota y mora desde Settings para que sea flexible.
     */
    public function getDeudaAttribute(): array
    {
        $montoCuota = (float) Setting::get('cuota_mensual', 10);
        $montoMora  = (float) Setting::get('mora_monto', 60);
        $mesesMora  = (int)   Setting::get('mora_meses', 3);

        $inicio = Carbon::parse($this->entry_date)->startOfMonth();
        $hoy    = Carbon::now()->startOfMonth();

        $mesesPendientes = $inicio->diffInMonths($hoy);
        $bloques         = $mesesMora > 0 ? floor($mesesPendientes / $mesesMora) : 0;
        $mora            = $bloques * $montoMora;

        return [
            'meses_cantidad' => $mesesPendientes,
            'subtotal'       => $mesesPendientes * $montoCuota,
            'mora'           => $mora,
            'total'          => ($mesesPendientes * $montoCuota) + $mora,
        ];
    }

    /**
     * ¿El socio está activo?
     */
    public function getIsActivoAttribute(): bool
    {
        return $this->status === 'activo';
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

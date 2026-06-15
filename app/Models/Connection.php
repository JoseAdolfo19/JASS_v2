<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Connection extends Model
{
    protected $fillable = [
        'associate_id',
        'sector_id',
        'label',
        'is_primary',
        'address',
        'meter_number',
        'active',
        'entry_date',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'active'     => 'boolean',
        'entry_date' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function payments(): HasMany
    {
        // Pagos vinculados a esta instalación específica
        return $this->hasMany(Payment::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    // Etiqueta completa: "LÓPEZ, Juan — Casa 2 (Sector Alto)"
    public function getDisplayLabelAttribute(): string
    {
        $nombre = strtoupper($this->associate->last_name ?? '')
                . ', ' . ($this->associate->name ?? '');
        $sector = $this->sector?->name ?? '';
        $label  = $this->is_primary ? 'Conexión Principal' : $this->label;
        return $sector ? "{$nombre} — {$label} ({$sector})" : "{$nombre} — {$label}";
    }

    // Para mostrar en tabla: solo el label con sector
    public function getBadgeLabelAttribute(): string
    {
        if ($this->is_primary) return '';
        $sector = $this->sector?->name ?? '';
        return $sector ? "{$this->label} · {$sector}" : $this->label;
    }
}
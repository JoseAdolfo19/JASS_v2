<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name'];

    // Relación: Un sector tiene muchos asociados
    public function associates()
    {
        return $this->hasMany(Associate::class);
    }
}
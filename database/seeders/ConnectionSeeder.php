<?php

namespace Database\Seeders;

use App\Models\Associate;
use App\Models\Connection;
use Illuminate\Database\Seeder;

/**
 * Crea la conexión principal para todos los socios existentes.
 * Ejecutar UNA SOLA VEZ: php artisan db:seed --class=ConnectionSeeder
 */
class ConnectionSeeder extends Seeder
{
    public function run(): void
    {
        $total = 0;

        Associate::with('sector')->each(function (Associate $asociado) use (&$total) {
            if ($asociado->connections()->where('is_primary', true)->exists()) return;

            $asociado->connections()->create([
                'sector_id'    => $asociado->sector_id,
                'label'        => 'Conexión Principal',
                'is_primary'   => true,
                'address'      => $asociado->address,
                'meter_number' => $asociado->meter_number,
                'active'       => true,
            ]);

            $total++;
        });

        $this->command->info("✓ {$total} conexiones primarias creadas.");
    }
}   
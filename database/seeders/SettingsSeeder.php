<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'cuota_mensual',    'value' => '10.00'],
            ['key' => 'mora_monto',       'value' => '60.00'],
            ['key' => 'mora_meses',       'value' => '3'],
            ['key' => 'jass_nombre',      'value' => 'JASS Huayoccary'],
            ['key' => 'jass_direccion',   'value' => ''],
            ['key' => 'jass_presidente',  'value' => ''],
            ['key' => 'jass_tesorero',    'value' => ''],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
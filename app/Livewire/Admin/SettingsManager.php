<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class SettingsManager extends Component
{
    // =========================================================================
    // TARIFAS
    // =========================================================================
    public string $cuota_mensual = '';
    public string $mora_monto    = '';
    public string $mora_meses    = '';

    // =========================================================================
    // DATOS DE LA JASS
    // =========================================================================
    public string $jass_nombre     = '';
    public string $jass_direccion  = '';
    public string $jass_presidente = '';
    public string $jass_tesorero   = '';

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->cuota_mensual  = Setting::get('cuota_mensual', '10.00');
        $this->mora_monto     = Setting::get('mora_monto', '60.00');
        $this->mora_meses     = Setting::get('mora_meses', '3');
        $this->jass_nombre    = Setting::get('jass_nombre', '');
        $this->jass_direccion = Setting::get('jass_direccion', '');
        $this->jass_presidente = Setting::get('jass_presidente', '');
        $this->jass_tesorero  = Setting::get('jass_tesorero', '');
    }

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    protected function rules(): array
    {
        return [
            'cuota_mensual'  => 'required|numeric|min:0.01',
            'mora_monto'     => 'required|numeric|min:0',
            'mora_meses'     => 'required|integer|min:1|max:24',
            'jass_nombre'    => 'required|string|max:100',
            'jass_direccion' => 'nullable|string|max:200',
            'jass_presidente'=> 'nullable|string|max:100',
            'jass_tesorero'  => 'nullable|string|max:100',
        ];
    }

    protected $messages = [
        'cuota_mensual.required' => 'La cuota mensual es obligatoria.',
        'cuota_mensual.numeric'  => 'La cuota debe ser un número.',
        'cuota_mensual.min'      => 'La cuota debe ser mayor a 0.',
        'mora_monto.required'    => 'El monto de mora es obligatorio.',
        'mora_meses.required'    => 'Los meses para mora son obligatorios.',
        'mora_meses.integer'     => 'Los meses deben ser un número entero.',
        'mora_meses.min'         => 'Debe ser al menos 1 mes.',
        'jass_nombre.required'   => 'El nombre de la JASS es obligatorio.',
    ];

    // =========================================================================
    // ACCIONES
    // =========================================================================

    public function saveTarifas(): void
    {
        $this->validateOnly('cuota_mensual');
        $this->validateOnly('mora_monto');
        $this->validateOnly('mora_meses');

        Setting::set('cuota_mensual', number_format((float) $this->cuota_mensual, 2, '.', ''));
        Setting::set('mora_monto',    number_format((float) $this->mora_monto, 2, '.', ''));
        Setting::set('mora_meses',    (string) intval($this->mora_meses));

        session()->flash('tarifas_ok', 'Tarifas actualizadas correctamente.');
    }

    public function saveJass(): void
    {
        $this->validateOnly('jass_nombre');
        $this->validateOnly('jass_direccion');
        $this->validateOnly('jass_presidente');
        $this->validateOnly('jass_tesorero');

        Setting::set('jass_nombre',     $this->jass_nombre);
        Setting::set('jass_direccion',  $this->jass_direccion);
        Setting::set('jass_presidente', $this->jass_presidente);
        Setting::set('jass_tesorero',   $this->jass_tesorero);

        session()->flash('jass_ok', 'Datos de la JASS actualizados correctamente.');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        return view('livewire.admin.settings-manager')
            ->layout('layouts.app');
    }
}
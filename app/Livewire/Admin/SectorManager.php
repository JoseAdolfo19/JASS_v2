<?php 

namespace App\Livewire\Admin;

use App\Models\Sector;
use Livewire\Component;

class SectorManager extends Component
{
    public $name; // Para el formulario de creación

    protected $rules = [
        'name' => 'required|min:3|unique:sectors,name',
    ];

    public function save()
    {
        $this->validate();
        Sector::create(['name' => $this->name]);
        $this->reset('name');
        session()->flash('message', 'Sector añadido con éxito.');
    }

    public function delete($id)
    {
        Sector::destroy($id);
    }

    public function render()
    {
        return view('livewire.admin.sector-manager', [
            'sectors' => Sector::all()
        ])->layout('layouts.app'); // Usamos nuestro layout de JASS_v2
    }
}
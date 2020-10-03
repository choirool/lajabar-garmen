<?php

namespace App\Http\Livewire\Material;

use App\Models\Material;
use Livewire\Component;

class CreateMaterial extends Component
{
    public $name;

    public function saveMaterial()
    {
        $this->validate([
            'name' => 'required|min:2|unique:materials',
        ]);

        Material::create([
            'name' => $this->name,
        ]);
        
        session()->flash('message', 'Material successfully created.');

        return redirect()->route('master-data.materials');
    }

    public function render()
    {
        return view('livewire.material.create-material');
    }
}

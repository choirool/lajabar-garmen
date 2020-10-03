<?php

namespace App\Http\Livewire\Material;

use App\Models\Material;
use Livewire\Component;

class UpdateMaterial extends Component
{
    public $material;
    public $name = '';

    public function mount($id)
    {
        $material = Material::findOrFail($id);
        $this->material = $material;
        $this->name = $material->name;
    }

    public function saveMaterial()
    {
        $this->validate([
            'name' => 'required|min:2|unique:materials,name,' . $this->material->id . ',id',
        ]);

        $this->material->name = $this->name;
        $this->material->save();

        session()->flash('message', 'Material successfully updated.');

        return redirect()->route('master-data.materials');
    }

    public function render()
    {
        return view('livewire.material.update-material');
    }
}

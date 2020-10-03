<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;

class CreateSize extends Component
{    
    public $name;

    public function saveSize()
    {
        $this->validate([
            'name' => 'required|min:1|unique:sizes',
        ]);

        Size::create([
            'name' => $this->name,
        ]);
        
        session()->flash('message', 'Size successfully created.');

        return redirect()->route('master-data.sizes');
    }

    public function render()
    {
        return view('livewire.size.create-size');
    }
}

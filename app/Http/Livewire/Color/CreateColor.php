<?php

namespace App\Http\Livewire\Color;

use App\Models\Color;
use Livewire\Component;

class CreateColor extends Component
{
    public $name;

    public function saveColor()
    {
        $this->validate([
            'name' => 'required|min:2|unique:colors',
        ]);

        Color::create([
            'name' => $this->name,
        ]);
        
        session()->flash('message', 'Color successfully created.');

        return redirect()->route('master-data.colors');
    }

    public function render()
    {
        return view('livewire.color.create-color');
    }
}

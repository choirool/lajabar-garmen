<?php

namespace App\Http\Livewire\Color;

use App\Models\Color;
use Livewire\Component;

class UpdateColor extends Component
{
    public $color;
    public $name = '';

    public function mount($id)
    {
        $color = Color::findOrFail($id);
        $this->color = $color;
        $this->name = $color->name;
    }

    public function saveColor()
    {
        $this->validate([
            'name' => 'required|min:2|unique:colors,name,' . $this->color->id . ',id',
        ]);

        $this->color->name = $this->name;
        $this->color->save();

        session()->flash('message', 'Color successfully updated.');

        return redirect()->route('master-data.colors');
    }

    public function render()
    {
        return view('livewire.color.update-color');
    }
}

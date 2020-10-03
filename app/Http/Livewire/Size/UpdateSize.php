<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;

class UpdateSize extends Component
{
    public $size;
    public $name = '';

    public function mount($id)
    {
        $size = Size::findOrFail($id);
        $this->size = $size;
        $this->name = $size->name;
    }

    public function saveSize()
    {
        $this->validate([
            'name' => 'required|min:1|unique:sizes,name,' . $this->size->id . ',id',
        ]);

        $this->size->name = $this->name;
        $this->size->save();

        session()->flash('message', 'Size successfully updated.');

        return redirect()->route('master-data.sizes');
    }

    public function render()
    {
        return view('livewire.size.update-size');
    }
}

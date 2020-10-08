<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;

class UpdateSize extends Component
{
    public $size;
    public $name = '';
    public $level = '';

    public function mount($id)
    {
        $size = Size::findOrFail($id);
        $this->size = $size;
        $this->name = $size->name;
        $this->level = $size->sort;
    }

    public function saveSize()
    {
        $this->validate([
            'name' => 'required|min:1|unique:sizes,name,' . $this->size->id . ',id',
            'level' => 'required|numeric|min:1',
        ]);

        $this->size->name = $this->name;
        $this->size->sort = $this->level;
        $this->size->save();

        session()->flash('message', 'Size successfully updated.');

        return redirect()->route('master-data.sizes');
    }

    public function render()
    {
        return view('livewire.size.update-size');
    }
}

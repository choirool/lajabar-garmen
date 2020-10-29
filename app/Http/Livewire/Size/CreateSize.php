<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;

class CreateSize extends Component
{    
    public $name;
    public $level;

    public function mount()
    {
        if(!auth()->user()->isAbleTo('size-create')) {
            abort(403);
        }

        $this->level = Size::withTrashed()->max('sort') +1;
    }

    public function saveSize()
    {
        $this->validate([
            'name' => 'required|min:1|unique:sizes',
            'level' => 'required|numeric|min:1',
        ]);

        Size::create([
            'name' => $this->name,
            'sort' => $this->level,
        ]);
        
        session()->flash('message', 'Size successfully created.');

        return redirect()->route('master-data.sizes');
    }

    public function render()
    {
        return view('livewire.size.create-size');
    }
}

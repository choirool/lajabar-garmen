<?php

namespace App\Http\Livewire\Salesman;

use App\Models\Salesman;
use Livewire\Component;

class CreateSalesman extends Component
{
    public $name;

    public function saveSalesman()
    {
        $this->validate([
            'name' => 'required|min:2|unique:salesmen',
        ]);

        Salesman::create([
            'name' => $this->name,
        ]);
        
        session()->flash('message', 'Salesman successfully created.');

        return redirect()->route('master-data.salesmen');
    }

    public function render()
    {
        return view('livewire.salesman.create-salesman');
    }
}

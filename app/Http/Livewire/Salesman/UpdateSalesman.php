<?php

namespace App\Http\Livewire\Salesman;

use App\Models\Salesman;
use Livewire\Component;

class UpdateSalesman extends Component
{
    public $color;
    public $name = '';

    public function mount($id)
    {
        $color = Salesman::findOrFail($id);
        $this->color = $color;
        $this->name = $color->name;
    }

    public function saveSalesman()
    {
        $this->validate([
            'name' => 'required|min:2|unique:salesmen,name,' . $this->color->id . ',id',
        ]);

        $this->color->name = $this->name;
        $this->color->save();

        session()->flash('message', 'Color successfully updated.');

        return redirect()->route('master-data.salesmen');
    }

    public function render()
    {
        return view('livewire.salesman.update-salesman');
    }
}

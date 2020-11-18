<?php

namespace App\Http\Livewire\Status;

use App\Models\Status;
use Livewire\Component;

class CreateStatus extends Component
{
    public $name;
    public $color;

    public function mount()
    {
        if(!auth()->user()->isAbleTo('status-create')) {
            abort(403);
        }
    }

    public function saveStatus()
    {
        $this->validate([
            'name' => 'required|min:1|unique:statuses',
            'color' => 'required',
        ]);

        Status::create([
            'name' => $this->name,
            'color' => $this->color,
        ]);
        
        session()->flash('message', 'Status successfully created.');

        return redirect()->route('master-data.statuses');
    }

    public function render()
    {
        return view('livewire.status.create-status');
    }
}

<?php

namespace App\Http\Livewire\Status;

use App\Models\Status;
use Livewire\Component;

class UpdateStatus extends Component
{
    public $status;
    public $name = '';
    public $color = '';

    public function mount($id)
    {
        if(!auth()->user()->isAbleTo('status-update')) {
            abort(403);
        }

        $status = Status::findOrFail($id);
        $this->status = $status;
        $this->name = $status->name;
        $this->color = $status->color;
    }

    public function saveStatus()
    {
        $this->validate([
            'name' => 'required|min:1|unique:sizes,name,' . $this->status->id . ',id',
            'color' => 'required',
        ]);

        $this->status->name = $this->name;
        $this->status->color = $this->color;
        $this->status->save();

        session()->flash('message', 'Status successfully updated.');

        return redirect()->route('master-data.statuses');
    }

    public function render()
    {
        return view('livewire.status.update-status');
    }
}

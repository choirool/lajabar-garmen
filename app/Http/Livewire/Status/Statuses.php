<?php

namespace App\Http\Livewire\Status;

use App\Models\Status;
use Livewire\Component;
use Livewire\WithPagination;

class Statuses extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;
    public $deleted = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if(!auth()->user()->isAbleTo('status-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Status::where('id', $id)->delete();
        $this->resetConfirm();
    }

    public function restore($id)
    {
        Status::where('id', $id)->restore();
        $this->resetConfirm();
    }

    public function confirm($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getStatus()
    {
        return Status::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->deleted, fn ($query) => $query->onlyTrashed())
            ->paginate();
    }

    public function render()
    {
        return view('livewire.status.statuses', [
            'statuses' => $this->getStatus()
        ]);
    }
}

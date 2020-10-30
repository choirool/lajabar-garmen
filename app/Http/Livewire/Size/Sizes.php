<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;
use Livewire\WithPagination;

class Sizes extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;
    public $deleted = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if(!auth()->user()->isAbleTo('size-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Size::where('id', $id)->delete();
        $this->resetConfirm();
    }

    public function restore($id)
    {
        Size::where('id', $id)->restore();
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

    protected function getSizes()
    {
        return Size::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->deleted, fn ($query) => $query->onlyTrashed())
            ->paginate();
    }
    
    public function render()
    {
        return view('livewire.size.sizes', [
            'sizes' => $this->getSizes()
        ]);
    }
}

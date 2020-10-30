<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;
    public $deleted = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('item-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Item::where('id', $id)->delete();
        $this->resetConfirm();
    }

    public function restore($id)
    {
        Item::where('id', $id)->restore();
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

    protected function getItems()
    {
        return Item::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->with('category', 'material')
            ->when($this->deleted, fn ($query) => $query->onlyTrashed())
            ->paginate();
    }

    public function render()
    {
        return view('livewire.item.item', [
            'items' => $this->getItems(),
        ]);
    }
}

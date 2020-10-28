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

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Item::where('id', $id)->delete();
    }

    public function confirmDelete($id)
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
            ->paginate();
    }

    public function render()
    {
        return view('livewire.item.item', [
            'items' => $this->getItems(),
        ]);
    }
}

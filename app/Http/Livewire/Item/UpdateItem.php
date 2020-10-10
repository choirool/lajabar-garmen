<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;
use App\Models\Category;
use App\Models\Item;

class UpdateItem extends Component
{
    public $item;
    public $name = '';
    public $unit = '';
    public $category = '';

    public function mount($id)
    {
        $item = Item::findOrFail($id);
        $this->item = $item;
        $this->name = $item->name;
        $this->unit = $item->unit;
        $this->category = $item->category_id;
    }

    public function saveItem()
    {
        $this->validate([
            'name' => 'required|min:2|unique:items,name,' . $this->item->id . ',id',
            'unit' => 'required',
            'category' => 'required|exists:categories,id',
        ]);

        $this->item->name = $this->name;
        $this->item->unit = $this->unit;
        $this->item->category_id = $this->category;
        $this->item->save();

        session()->flash('message', 'item successfully updated.');

        return redirect()->route('master-data.items');
    }

    public function render()
    {
        return view('livewire.item.update-item', [
            'categories' => Category::all()
        ]);
    }
}

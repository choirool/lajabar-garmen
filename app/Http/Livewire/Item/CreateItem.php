<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use App\Models\Category;

class CreateItem extends Component
{
    public $name;
    public $unit;
    public $category;

    public function saveItem()
    {
        $this->validate([
            'name' => 'required|min:2|unique:items',
            'unit' => 'required',
            'category' => 'required|exists:categories,id'
        ]);

        Item::create([
            'name' => $this->name,
            'unit' => $this->unit,
            'category_id' => $this->category,
        ]);
        
        session()->flash('message', 'Item successfully created.');

        return redirect()->route('master-data.items');
    }

    public function render()
    {
        return view('livewire.item.create-item', [
            'categories' => Category::all()
        ]);
    }
}

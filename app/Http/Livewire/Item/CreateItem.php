<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use App\Models\Category;
use App\Models\Material;

class CreateItem extends Component
{
    public $name;
    public $unit;
    public $category;
    public $material;

    public function saveItem()
    {
        $this->validate([
            'name' => 'required|min:2|unique:items',
            'unit' => 'required',
            'category' => 'required|exists:categories,id',
            'material' => 'required|exists:materials,id',
        ]);

        Item::create([
            'name' => $this->name,
            'unit' => $this->unit,
            'category_id' => $this->category,
            'material_id' => $this->material,
        ]);
        
        session()->flash('message', 'Item successfully created.');

        return redirect()->route('master-data.items');
    }

    public function render()
    {
        return view('livewire.item.create-item', [
            'categories' => Category::all(),
            'materials' => Material::all(),
        ]);
    }
}

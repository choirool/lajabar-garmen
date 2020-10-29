<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use App\Models\Category;
use App\Models\Material;

class UpdateItem extends Component
{
    public $item;
    public $name = '';
    public $unit = '';
    public $category = '';
    public $material = '';

    public function mount($id)
    {
        if (!auth()->user()->isAbleTo('item-update')) {
            abort(403);
        }

        $item = Item::findOrFail($id);
        $this->item = $item;
        $this->name = $item->name;
        $this->unit = $item->unit;
        $this->category = $item->category_id;
        $this->material = $item->material_id;
    }

    public function saveItem()
    {
        $this->validate([
            'name' => 'required|min:2|unique:items,name,' . $this->item->id . ',id',
            'unit' => 'required',
            'category' => 'required|exists:categories,id',
            'material' => 'required|exists:materials,id',
        ]);

        $this->item->name = $this->name;
        $this->item->unit = $this->unit;
        $this->item->category_id = $this->category;
        $this->item->material_id = $this->material;
        $this->item->save();

        session()->flash('message', 'item successfully updated.');

        return redirect()->route('master-data.items');
    }

    public function render()
    {
        return view('livewire.item.update-item', [
            'categories' => Category::all(),
            'materials' => Material::all(),
        ]);
    }
}

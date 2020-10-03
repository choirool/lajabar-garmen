<?php

namespace App\Http\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class UpdateCategory extends Component
{
    public $category;
    public $name = '';

    public function mount($id)
    {
        $category = Category::findOrFail($id);
        $this->category = $category;
        $this->name = $category->name;
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|min:2|unique:categories,name,' . $this->category->id . ',id',
        ]);

        $this->category->name = $this->name;
        $this->category->save();

        session()->flash('message', 'Category successfully updated.');

        return redirect()->route('master-data.categories');
    }

    public function render()
    {
        return view('livewire.category.update-category');
    }
}

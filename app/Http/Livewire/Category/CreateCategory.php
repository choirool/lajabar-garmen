<?php

namespace App\Http\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class CreateCategory extends Component
{
    public $name;

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|min:2|unique:categories',
        ]);

        Category::create([
            'name' => $this->name,
        ]);
        
        session()->flash('message', 'Category successfully created.');

        return redirect()->route('master-data.categories');
    }

    public function render()
    {
        return view('livewire.category.create-category');
    }
}

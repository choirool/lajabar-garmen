<?php

namespace App\Http\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
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
        Category::where('id', $id)->delete();
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getCategories()
    {
        return Category::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate();
    }
    
    public function render()
    {
        return view('livewire.category.categories', [
            'categories' => $this->getCategories()
        ]);
    }
}

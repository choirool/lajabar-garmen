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
    public $deleted = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('type-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Category::where('id', $id)->delete();
        $this->resetConfirm();
    }

    public function restore($id)
    {
        Category::where('id', $id)->restore();
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

    protected function getCategories()
    {
        return Category::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->deleted, fn ($query) => $query->onlyTrashed())
            ->paginate();
    }

    public function render()
    {
        return view('livewire.category.categories', [
            'categories' => $this->getCategories()
        ]);
    }
}

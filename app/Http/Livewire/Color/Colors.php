<?php

namespace App\Http\Livewire\Color;

use App\Models\Color;
use Livewire\Component;
use Livewire\WithPagination;

class Colors extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;
    public $deleted = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('color-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function render()
    {
        return view('livewire.color.colors', [
            'colors' => $this->getColors()
        ]);
    }

    public function delete($id)
    {
        Color::where('id', $id)->delete();
        $this->resetConfirm();
    }

    public function restore($id)
    {
        Color::where('id', $id)->restore();
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

    protected function getColors()
    {
        return Color::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->deleted, fn ($query) => $query->onlyTrashed())
            ->paginate();
    }
}

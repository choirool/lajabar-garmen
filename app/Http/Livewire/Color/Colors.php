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
    }

    public function confirmDelete($id)
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
            ->paginate();
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Test;
use App\Models\User;
use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ManageTests extends Component
{
    use WithPagination;

    public $search = '';



    public function delete($id)
    {
        abort_if(!auth()->user()->is_admin, 403);
        Test::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-test-delete');
    }

    public function render()
    {
        $tests = Test::query()
            ->with(['user', 'quiz'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('quiz', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manage-tests', compact('tests'));
    }
}

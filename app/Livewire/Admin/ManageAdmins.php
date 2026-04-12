<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ManageAdmins extends Component
{
    use WithPagination;

    public $search = '';

    public function boot()
    {
        abort_if(!auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function toggleAdmin($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent removing their own admin status by accident
        if (auth()->id() === $user->id) {
            return;
        }

        $user->update(['is_admin' => !$user->is_admin]);
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-admins', compact('users'));
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Models\Test;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageTests extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $testId = null;

    public $user_id = '';

    public $quiz_id = '';

    public $result = '';

    public $ip_address = '';

    public $time_spent = '';

    public function mount()
    {
        abort_if(! auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->reset(['testId', 'user_id', 'quiz_id', 'result', 'ip_address', 'time_spent']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'test-form');
    }

    public function edit(Test $test)
    {
        $this->resetForm();
        $this->testId = $test->id;
        $this->user_id = $test->user_id;
        $this->quiz_id = $test->quiz_id;
        $this->result = $test->result;
        $this->ip_address = $test->ip_address;
        $this->time_spent = $test->time_spent;

        $this->dispatch('open-modal', 'test-form');
    }

    public function save()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'result' => 'required|integer|min:0',
            'ip_address' => 'nullable|string|max:45|ip',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        Test::updateOrCreate(
            ['id' => $this->testId],
            [
                'user_id' => $this->user_id,
                'quiz_id' => $this->quiz_id,
                'result' => $this->result,
                'ip_address' => $this->ip_address,
                'time_spent' => $this->time_spent,
            ]
        );

        $this->dispatch('close-modal', 'test-form');
        $this->resetForm();
    }

    public function delete($id)
    {
        abort_if(! auth()->user()->is_admin, 403);
        Test::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-test-delete');
    }

    public function render()
    {
        $tests = Test::query()
            ->with(['user', 'quiz'])
            ->search($this->search)
            ->latest()
            ->paginate(10);

        $users = User::orderBy('name')->get();
        $quizzes = Quiz::orderBy('title')->get();

        return view('livewire.admin.manage-tests', compact('tests', 'users', 'quizzes'));
    }
}

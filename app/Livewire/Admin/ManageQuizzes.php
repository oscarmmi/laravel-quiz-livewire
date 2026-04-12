<?php

namespace App\Livewire\Admin;

use App\Livewire\Forms\QuizForm;
use App\Models\Quiz;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageQuizzes extends Component
{
    use WithPagination;

    public $search = '';

    public QuizForm $form;

    public function mount()
    {
        abort_if(! auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->form->reset();
        $this->form->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'quiz-form');
    }

    public function edit(Quiz $quiz)
    {
        $this->resetForm();
        $this->form->setQuiz($quiz);
        $this->dispatch('open-modal', 'quiz-form');
    }

    public function save()
    {
        $this->form->store();
        $this->dispatch('close-modal', 'quiz-form');
    }

    public function delete($id)
    {
        abort_if(! auth()->user()->is_admin, 403);
        Quiz::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-quiz-delete');
    }

    public function render()
    {
        $quizzes = Quiz::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manage-quizzes', compact('quizzes'));
    }
}

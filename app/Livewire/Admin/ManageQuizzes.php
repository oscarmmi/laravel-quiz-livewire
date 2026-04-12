<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageQuizzes extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $quizId = null;

    public $title = '';

    public $slug = '';

    public $description = '';

    public $published = false;

    public $public = false;

    public function mount()
    {
        abort_if(! auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->reset(['quizId', 'title', 'slug', 'description', 'published', 'public']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'quiz-form');
    }

    public function edit(Quiz $quiz)
    {
        $this->resetForm();
        $this->quizId = $quiz->id;
        $this->title = $quiz->title;
        $this->slug = $quiz->slug;
        $this->description = $quiz->description;
        $this->published = $quiz->published;
        $this->public = $quiz->public;

        $this->dispatch('open-modal', 'quiz-form');
    }

    public function save()
    {
        // Auto-generate slug if it's completely empty
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|max:255|unique:quizzes,slug,'.$this->quizId,
            'description' => 'nullable|string',
            'published' => 'boolean',
            'public' => 'boolean',
        ]);

        Quiz::updateOrCreate(
            ['id' => $this->quizId],
            [
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'published' => $this->published,
                'public' => $this->public,
            ]
        );

        $this->dispatch('close-modal', 'quiz-form');
        $this->resetForm();
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

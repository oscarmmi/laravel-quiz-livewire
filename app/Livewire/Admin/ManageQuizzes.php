<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

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
        abort_if(!auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->reset(['quizId', 'title', 'slug', 'description', 'published', 'public']);
        $this->resetValidation();
    }

    public function save()
    {
        // Auto-generate slug if it's completely empty
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|max:255|unique:quizzes,slug,' . $this->quizId,
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
                'created_by' => \App\Enums\TestCreatedBy::Admin,
                'user_id' => auth()->id(),
            ]
        );

        $this->dispatch('quiz-saved');
        $this->resetForm();
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->is_admin, 403);
        Quiz::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-quiz-delete');
    }

    public function saveQuestions($quizId, $questionIds)
    {
        abort_if(!auth()->user()->is_admin, 403);
        
        $quiz = Quiz::findOrFail($quizId);
        $quiz->questions()->sync($questionIds);
        
        $this->dispatch('quiz-questions-saved');
    }

    public function render()
    {
        $quizzes = Quiz::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with('questions')
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();
        $allQuestions = Question::with('categories')->select('id', 'question_text')->orderBy('id', 'desc')->get();

        return view('livewire.admin.manage-quizzes', compact('quizzes', 'categories', 'allQuestions'));
    }
}

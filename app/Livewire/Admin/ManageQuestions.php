<?php

namespace App\Livewire\Admin;

use App\Models\Question;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ManageQuestions extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $questionId = null;
    public $question_text = '';
    public $code_snippet = '';
    public $answer_explanation = '';
    public $more_info_link = '';
    public $category_id = '';

    public function mount()
    {
        abort_if(!auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->reset(['questionId', 'question_text', 'code_snippet', 'answer_explanation', 'more_info_link', 'category_id']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'question_text' => 'required|string',
            'code_snippet' => 'nullable|string',
            'answer_explanation' => 'nullable|string',
            'more_info_link' => 'nullable|url',
            'category_id' => 'required|exists:categories,id',
        ]);

        Question::updateOrCreate(
            ['id' => $this->questionId],
            [
                'question_text' => $this->question_text,
                'code_snippet' => $this->code_snippet,
                'answer_explanation' => $this->answer_explanation,
                'more_info_link' => $this->more_info_link,
                'category_id' => $this->category_id,
            ]
        );

        $this->resetForm();
        $this->dispatch('question-saved');
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->is_admin, 403);
        Question::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-question-delete');
    }

    public function render()
    {
        $questions = Question::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where('question_text', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.manage-questions', compact('questions', 'categories'));
    }
}

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
    public $type = 'unique-answer';
    public $category_ids = [];

    public function mount()
    {
        abort_if(!auth()->user()->is_admin, 403, 'Unauthorized action.');
    }

    public function resetForm()
    {
        $this->reset(['questionId', 'question_text', 'code_snippet', 'answer_explanation', 'more_info_link', 'type', 'category_ids']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'question_text' => 'required|string',
            'code_snippet' => 'nullable|string',
            'answer_explanation' => 'nullable|string',
            'more_info_link' => 'nullable|url',
            'type' => 'required|in:single-line,multi-line,unique-answer,multi-answer',
            'category_ids' => 'required|array|min:1',
        ]);

        $question = Question::updateOrCreate(
            ['id' => $this->questionId],
            [
                'question_text' => $this->question_text,
                'code_snippet' => $this->code_snippet,
                'answer_explanation' => $this->answer_explanation,
                'more_info_link' => $this->more_info_link,
                'type' => $this->type,
            ]
        );

        $finalCategoryIds = [];
        foreach ($this->category_ids as $item) {
            if (is_numeric($item)) {
                if (Category::find($item)) {
                    $finalCategoryIds[] = $item;
                    continue;
                }
            }
            
            // If it's not a numeric ID (or the ID didn't exist), create a new Category
            $newCat = Category::firstOrCreate(['name' => trim($item)]);
            $finalCategoryIds[] = $newCat->id;
        }

        $question->categories()->sync($finalCategoryIds);

        $this->resetForm();
        $this->dispatch('question-saved');
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->is_admin, 403);
        Question::findOrFail($id)->delete();
        $this->dispatch('close-modal', 'confirm-question-delete');
    }

    public function saveOptions($questionId, $options)
    {
        abort_if(!auth()->user()->is_admin, 403);
        $question = Question::findOrFail($questionId);

        $existingIds = collect($options)->pluck('id')->filter()->toArray();
        
        // Remove options that are no longer there
        $question->options()->whereNotIn('id', $existingIds)->delete();

        foreach ($options as $opt) {
            if (isset($opt['id']) && $opt['id']) {
                $option = $question->options()->find($opt['id']);
                if ($option) {
                    $option->update([
                        'text' => $opt['text'],
                        'correct' => (bool)$opt['correct'],
                    ]);
                }
            } else {
                $question->options()->create([
                    'text' => $opt['text'],
                    'correct' => (bool)$opt['correct'],
                ]);
            }
        }

        $this->dispatch('options-saved');
    }

    public function render()
    {
        $questions = Question::query()
            ->with(['categories', 'options'])
            ->when($this->search, function ($query) {
                $query->where('question_text', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.manage-questions', compact('questions', 'categories'));
    }
}

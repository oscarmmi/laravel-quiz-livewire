<?php

namespace App\Livewire\Forms;

use App\Models\Quiz;
use Illuminate\Support\Str;
use Livewire\Form;

class QuizForm extends Form
{
    public $quizId = null;

    public $title = '';

    public $slug = '';

    public $description = '';

    public $published = false;

    public $public = false;

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|max:255|unique:quizzes,slug,'.$this->quizId,
            'description' => 'nullable|string',
            'published' => 'boolean',
            'public' => 'boolean',
        ];
    }

    public function setQuiz(Quiz $quiz)
    {
        $this->quizId = $quiz->id;
        $this->title = $quiz->title;
        $this->slug = $quiz->slug;
        $this->description = $quiz->description;
        $this->published = $quiz->published;
        $this->public = $quiz->public;
    }

    public function store()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }

        $this->validate();

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

        $this->reset();
    }
}

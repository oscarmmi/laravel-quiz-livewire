<?php

namespace App\Livewire\Quiz;

use App\Models\Quiz;
use App\Models\Test;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TakeTest extends Component
{
    public Quiz $quiz;
    public Test $test;
    public array $answers = [];

    public function mount(Test $test)
    {
        $this->test = $test;
        $this->quiz = $test->quiz->load('questions.options');
    }

    public function render()
    {
        return view('livewire.quiz.take-test');
    }
}

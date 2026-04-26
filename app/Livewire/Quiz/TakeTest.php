<?php

namespace App\Livewire\Quiz;

use App\Models\Quiz;
use App\Models\Test;
use App\Models\Answer;
use App\Models\Option;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TakeTest extends Component
{
    public Quiz $quiz;
    public Test $test;
    public array $answers = [];
    public bool $isSubmitted = false;
    public int $score = 0;

    public function mount(Test $test)
    {
        $this->test = $test;
        $this->quiz = $test->quiz->load('questions.options');
        
        if (Answer::where('test_id', $this->test->id)->exists()) {
            $this->isSubmitted = true;
            $this->score = $this->test->result;
        }
    }

    public function submit()
    {
        $totalScore = 0;

        foreach ($this->answers as $questionId => $answerData) {
            // Check if answer is an array (multi-answer) or single value
            if (is_array($answerData)) {
                foreach ($answerData as $optionId => $isSelected) {
                    if ($isSelected) {
                        $this->saveAnswer($questionId, $optionId, $totalScore);
                    }
                }
            } else {
                // $answerData is the optionId
                if (is_numeric($answerData)) {
                    $this->saveAnswer($questionId, $answerData, $totalScore);
                }
            }
        }

        $this->test->update([
            'result' => $totalScore,
        ]);

        $this->score = $totalScore;
        $this->isSubmitted = true;
    }

    private function saveAnswer($questionId, $optionId, &$totalScore)
    {
        $option = Option::find($optionId);
        $isCorrect = $option ? $option->correct : false;

        if ($isCorrect) {
            $totalScore++;
        }

        Answer::create([
            'user_id' => auth()->id(),
            'test_id' => $this->test->id,
            'question_id' => $questionId,
            'option_id' => $optionId,
            'correct' => $isCorrect,
        ]);
    }

    public function render()
    {
        return view('livewire.quiz.take-test');
    }
}

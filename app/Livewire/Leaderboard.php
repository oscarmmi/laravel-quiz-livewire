<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Test;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Leaderboard extends Component
{
    public $quiz_id = 0;

    public function render()
    {
        $quizzes = Quiz::public()->get();

        $tests = Test::query()
            ->whereHas('user')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'quiz' => function ($query) {
                $query->select('id', 'title');
                $query->withCount('questions');
            }])
            ->when($this->quiz_id > 0, function ($query) {
                $query->where('quiz_id', $this->quiz_id);
            })
            ->orderBy('result', 'desc')
            ->orderBy('time_spent')
            ->get();

        return view('livewire.leaderboard', [
            'tests' => $tests,
            'quizzes' => $quizzes,
        ]);
    }
}

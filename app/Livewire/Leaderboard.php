<?php

namespace App\Livewire;

use App\Models\Test;
use App\Models\Quiz;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Leaderboard extends Component
{
    public $quiz_id = 0;

    public function render()
    {
        $quizzes = Quiz::public()->get();

        $tests = Test::query()
            ->select('user_id')
            ->selectRaw('AVG(result) as avg_result')
            ->selectRaw('SUM(time_spent) as total_time_spent')
            ->whereHas('user')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->when($this->quiz_id > 0, function ($query) {
                $query->where('quiz_id', $this->quiz_id);
            })
            ->groupBy('user_id')
            ->orderByDesc('avg_result')
            ->orderBy('total_time_spent')
            ->get();

        return view('livewire.leaderboard', [
            'tests' => $tests,
            'quizzes' => $quizzes
        ]);
    }
}

<?php

use Livewire\Volt\Component;
use App\Models\Test;
use App\Models\Quiz;

new class extends Component {
    public int $quizId;

    public function start()
    {
        $test = Test::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quizId,
            'result' => 0,
            'ip_address' => request()->ip(),
        ]);

        $this->redirectRoute('test.show', ['test' => $test->id], navigate: true);
    }
}; ?>

<button wire:click="start" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
    Take Quiz 
    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
</button>

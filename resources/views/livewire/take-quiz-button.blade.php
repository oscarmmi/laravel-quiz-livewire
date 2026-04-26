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
<div>
    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-quiz-creation-{{ $quizId }}')" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
        Take Quiz 
        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>

    <x-modal name="confirm-quiz-creation-{{ $quizId }}" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Start Quiz') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Are you sure you want to start this quiz now? A new test attempt will be created.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button wire:click="start" class="ms-3">
                    {{ __('Take Quiz') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>

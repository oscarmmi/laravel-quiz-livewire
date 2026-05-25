<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if($isSubmitted)
                    <div class="text-center py-8">
                        <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">Quiz Completed!</h2>
                        <p class="text-gray-600 mb-6">You have successfully finished the quiz.</p>
                        
                        <div class="bg-gray-50 rounded-lg p-6 max-w-md mx-auto mb-8 border border-gray-200">
                            <p class="text-lg font-medium text-gray-800">Your Score</p>
                            <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $score }} %</p>
                        </div>

                        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Dashboard
                        </a>
                    </div>
                @else
                    <h2 class="text-2xl font-bold mb-4">{{ $quiz->title }}</h2>
                    <p class="mb-6">{{ $quiz->description }}</p>

                    <div x-data="{ step: 0, total: {{ $quiz->questions->count() }} }">
                        <!-- Progress Bar -->
                        <div class="mb-2 bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" x-bind:style="'width: ' + ((step + 1) / total * 100) + '%'"></div>
                        </div>
                        <div class="mb-6 text-sm text-gray-500 text-right">
                            Question <span x-text="step + 1"></span> of <span x-text="total"></span>
                        </div>

                        <div class="space-y-8 min-h-[250px]">
                            @foreach($quiz->questions as $index => $question)
                                <div class="border-t pt-6" x-show="step === {{ $index }}" x-cloak>
                                    <h3 class="text-lg font-semibold mb-3">{{ $question->question_text }}</h3>
                                    
                                    @if($question->answer_explanation)
                                        <div class="mt-6 p-4 bg-indigo-50 border-l-4 border-indigo-400 text-indigo-800 rounded-r-lg">
                                            <p class="font-bold flex items-center text-sm uppercase tracking-wide">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Explanation
                                            </p>
                                            <p class="mt-1 text-sm">{{ $question->answer_explanation }}</p>
                                        </div>
                                    @endif

                                    @if($question->code_snippet)
                                        <div class="mb-4 bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto font-mono text-sm">
                                            <pre><code>{{ $question->code_snippet }}</code></pre>
                                        </div>
                                    @endif

                                    @if($question->type === \App\Enums\QuestionType::SingleLine)
                                        <x-text-input wire:model="answers.{{ $question->id }}" type="text" class="w-full mt-2" placeholder="Type your answer here..." />
                                    @elseif($question->type === \App\Enums\QuestionType::MultiLine)
                                        <textarea wire:model="answers.{{ $question->id }}" class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" placeholder="Type your detailed answer here..."></textarea>
                                    @elseif($question->type === \App\Enums\QuestionType::UniqueAnswer)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                            @foreach($question->options as $option)
                                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                    <input type="radio" wire:model="answers.{{ $question->id }}" name="question_{{ $question->id }}" value="{{ $option->id }}" class="text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-3">{{ $option->text }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === \App\Enums\QuestionType::MultiAnswer)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                            @foreach($question->options as $option)
                                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                    <input type="checkbox" wire:model="answers.{{ $question->id }}.{{ $option->id }}" value="{{ $option->id }}" class="text-indigo-600 focus:ring-indigo-500 rounded border-gray-300">
                                                    <span class="ml-3">{{ $option->text }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-between gap-3 border-t pt-6">
                            <div class="flex gap-3">
                                <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="button" x-show="step > 0" x-on:click="step--" x-cloak class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Back
                                </button>
                            </div>
                            
                            <div class="flex gap-3">
                                <x-primary-button type="button" x-show="step < (total - 1)" x-on:click.prevent="step++">
                                    Next
                                </x-primary-button>
<div x-show="step >= (total - 1)">
                                        <x-primary-button x-on:click="$dispatch('open-modal', 'confirm-submit')">
                                            Submit Quiz
                                        </x-primary-button>
                                    </div>

                                    <x-modal name="confirm-submit" :show="false">
                                        <div class="p-6">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                Submit Quiz?
                                            </h3>
                                            <p class="mt-2 text-sm text-gray-600">
                                                Are you sure you want to submit your quiz? Once submitted, you cannot change your answers.
                                            </p>
                                            <div class="mt-6 flex justify-end gap-3">
                                                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-submit')">
                                                    Cancel
                                                </x-secondary-button>
                                                <x-primary-button wire:click="submit" x-on:click="$dispatch('close-modal', 'confirm-submit')">
                                                    Confirm Submit
                                                </x-primary-button>
                                            </div>
                                        </div>
                                    </x-modal>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

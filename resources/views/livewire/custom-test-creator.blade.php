<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Custom Test Creator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(!$isSubmitted)
                        <form wire:submit.prevent="submit" x-data="{
                            get selectedCount() {
                                return $wire.sourceType === 'categories' 
                                    ? ($wire.selectedCategories ? $wire.selectedCategories.length : 0)
                                    : ($wire.selectedQuizzes ? $wire.selectedQuizzes.length : 0);
                            },
                            get canSubmit() {
                                return this.selectedCount > 0 && $wire.totalQuestions > 0;
                            }
                        }">
                            <div class="space-y-6">
                                <!-- Source Type Switch -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Question Source <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative flex p-1 bg-gray-100/80 rounded-lg border border-gray-200/60">
                                        <!-- Categories Option -->
                                        <label class="relative flex-1 cursor-pointer">
                                            <input type="radio" wire:model.live="sourceType" value="categories" class="sr-only">
                                            <div class="py-2 text-sm font-medium text-center transition-all duration-200 rounded-md"
                                                 :class="$wire.sourceType === 'categories' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-gray-900/5' : 'text-gray-500 hover:text-gray-700'">
                                                Categories
                                            </div>
                                        </label>

                                        <!-- Quizzes Option -->
                                        <label class="relative flex-1 cursor-pointer">
                                            <input type="radio" wire:model.live="sourceType" value="quizzes" class="sr-only">
                                            <div class="py-2 text-sm font-medium text-center transition-all duration-200 rounded-md"
                                                 :class="$wire.sourceType === 'quizzes' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-gray-900/5' : 'text-gray-500 hover:text-gray-700'">
                                                Existing Quizzes
                                            </div>
                                        </label>
                                    </div>
                                    @error('sourceType')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Selection List -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select <span x-text="$wire.sourceType === 'categories' ? 'Categories' : 'Quizzes'"></span> <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <!-- Categories List -->
                                    <div x-show="$wire.sourceType === 'categories'" class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                                        @if($categories->isEmpty())
                                            <p class="text-sm text-gray-500 text-center py-4">
                                                No categories with questions available.
                                            </p>
                                        @else
                                            <div class="space-y-2">
                                                @foreach($categories as $category)
                                                    <label class="flex items-center p-2 rounded hover:bg-gray-100 cursor-pointer transition-colors">
                                                        <input
                                                            type="checkbox"
                                                            wire:model="selectedCategories"
                                                            value="{{ $category->id }}"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                        >
                                                        <span class="ml-3 text-sm text-gray-700">
                                                            {{ $category->name }}
                                                        </span>
                                                        <span class="ml-auto text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">
                                                            {{ $category->questions_count }} questions
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Quizzes List -->
                                    <div x-show="$wire.sourceType === 'quizzes'" style="display: none;" class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                                        @if($quizzes->isEmpty())
                                            <p class="text-sm text-gray-500 text-center py-4">
                                                No quizzes available.
                                            </p>
                                        @else
                                            <div class="space-y-2">
                                                @foreach($quizzes as $quiz)
                                                    <label class="flex items-center p-2 rounded hover:bg-gray-100 cursor-pointer transition-colors">
                                                        <input
                                                            type="checkbox"
                                                            wire:model="selectedQuizzes"
                                                            value="{{ $quiz->id }}"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                        >
                                                        <span class="ml-3 text-sm text-gray-700">
                                                            {{ $quiz->title }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @error('selectedCategories')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('selectedQuizzes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">
                                        Selected: <span x-text="selectedCount" class="font-medium text-indigo-600">0</span> item(s)
                                    </p>
                                </div>

                                <!-- Total Questions Input -->
                                <div>
                                    <label for="totalQuestions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Total Questions <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="totalQuestions"
                                        wire:model="totalQuestions"
                                        min="1"
                                        max="1000"
                                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                                        :class="{'border-red-300': {{ $totalQuestions }} < 1}"
                                        placeholder="Enter number of questions"
                                    >
                                    @error('totalQuestions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">
                                        Questions will be distributed as equally as possible among selected categories.
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
<x-primary-button
                    type="submit"
                    class="w-full justify-center py-3"
                    x-bind:disabled="!canSubmit"
                >
                                        {{ __('Generate Custom Test') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Success State -->
                        <div class="text-center py-6">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                Test Generated Successfully!
                            </h3>
                            <p class="text-sm text-gray-500 mb-6">
                                A total of <span class="font-semibold text-indigo-600">{{ $actualTotalQuestions }}</span> questions have been assigned to the quiz.
                            </p>

                            <!-- Distribution Summary -->
                            @if(count($distributionSummary) > 0)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden mb-6">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Category
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Requested
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Actual
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($distributionSummary as $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        {{ $item['category'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 text-center">
                                                        {{ $item['requested'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-center">
                                                        @if($item['actual'] < $item['requested'])
                                                            <span class="text-red-600 font-medium">{{ $item['actual'] }}</span>
                                                        @else
                                                            <span class="text-green-600 font-medium">{{ $item['actual'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-3 justify-center">
                                <x-secondary-button wire:click="resetForm">
                                    Create Another Test
                                </x-secondary-button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
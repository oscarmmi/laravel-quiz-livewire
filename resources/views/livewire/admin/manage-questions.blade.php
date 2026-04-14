<div x-data="{
    showForm: false,
    openCreate() {
        this.showForm = true;
        // Limpiamos los campos en el navegador, sin esperar a Livewire
        $wire.questionId = null;
        $wire.question_text = '';
        $wire.code_snippet = '';
        $wire.answer_explanation = '';
        $wire.more_info_link = '';
        $wire.category_id = '';
    },
    openEdit(question) {
        this.showForm = true;
        // Inyectamos los datos en el navegador instantáneamente
        $wire.questionId = question.id;
        $wire.question_text = question.question_text;
        $wire.code_snippet = question.code_snippet;
        $wire.answer_explanation = question.answer_explanation;
        $wire.more_info_link = question.more_info_link;
        $wire.category_id = question.category_id;
    }
}" @question-saved.window="showForm = false" x-cloak>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Questions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- List View -->
            <div x-show="!showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search questions..." class="w-full sm:w-1/2" />
                        
                        <!-- Cero llamadas a Livewire. Todo puramente en Javascript -->
                        <x-primary-button @click="openCreate()" type="button">
                            {{ __('Add New Question') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Question</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($questions as $question)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <div class="line-clamp-2">{{ $question->question_text }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $question->category ? $question->category->name : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <!-- Cero llamadas a Livewire para rellenar datos. Los inyectamos en JS -->
                                            <button @click="openEdit(@js($question))" type="button" class="text-indigo-600 hover:text-indigo-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                                Edit
                                            </button>
                                            <button x-data x-on:click="$dispatch('open-delete-modal', {{ $question->id }})" class="text-red-600 hover:text-red-900 focus:outline-none transition duration-150 ease-in-out">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-500">No questions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($questions->hasPages())
                            <div class="p-4 border-t border-gray-200">
                                {{ $questions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form View -->
            <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="relative">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form wire:submit.prevent="save" class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            <span x-text="$wire.questionId ? 'Edit Question' : 'Create New Question'"></span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="category_id" value="Category" />
                                <select wire:model="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select a Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="question_text" value="Question Text" />
                                <textarea wire:model="question_text" id="question_text" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="code_snippet" value="Code Snippet (Optional)" />
                                <textarea wire:model="code_snippet" id="code_snippet" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm shadow-inner bg-gray-50"></textarea>
                                <x-input-error :messages="$errors->get('code_snippet')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="answer_explanation" value="Answer Explanation (Optional)" />
                                <textarea wire:model="answer_explanation" id="answer_explanation" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <x-input-error :messages="$errors->get('answer_explanation')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="more_info_link" value="More Info Link (Optional)" />
                                <x-text-input wire:model="more_info_link" id="more_info_link" type="url" class="mt-1 block w-full" placeholder="https://" />
                                <x-input-error :messages="$errors->get('more_info_link')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <!-- Cierre puramente en el frontend -->
                            <x-secondary-button @click="showForm = false" type="button">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">{{ __('Save Question') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal Alpine Wrapper -->
    <div x-data="{ questionIdToDelete: null }" @open-delete-modal.window="questionIdToDelete = $event.detail; $dispatch('open-modal', 'confirm-question-delete')">
        <x-modal name="confirm-question-delete" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this question?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    This action is permanent and cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button x-on:click="$wire.delete(questionIdToDelete)" class="ms-3">
                        {{ __('Yes, Delete Question') }}
                    </x-danger-button>
                </div>
            </div>
        </x-modal>
    </div>
</div>

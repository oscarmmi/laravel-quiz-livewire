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
        $wire.category_ids = [];
    },
    openEdit(question) {
        this.showForm = true;
        // Inyectamos los datos en el navegador instantáneamente
        $wire.questionId = question.id;
        $wire.question_text = question.question_text;
        $wire.code_snippet = question.code_snippet;
        $wire.answer_explanation = question.answer_explanation;
        $wire.more_info_link = question.more_info_link;
        $wire.category_ids = question.categories ? question.categories.map(c => c.id) : [];
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
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Question</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categories</th>
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
                                            {{ $question->categories->isNotEmpty() ? $question->categories->pluck('name')->join(', ') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
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
                                <x-input-label for="category_search" value="Categories" />
                                
                                <div x-data="{
                                    search: '',
                                    allCategories: @js($categories),
                                    get selected() {
                                        if (!$wire.category_ids || !Array.isArray($wire.category_ids)) return [];
                                        return $wire.category_ids.map(item => {
                                            let cats = Array.isArray(this.allCategories) ? this.allCategories : Object.values(this.allCategories);
                                            let existing = cats.find(c => String(c.id) === String(item?.id || item));
                                            return existing ? existing : { id: item, name: item?.name || item };
                                        });
                                    },
                                    get filtered() {
                                        if (this.search === '') return [];
                                        let s = this.search.toLowerCase();
                                        let cats = Array.isArray(this.allCategories) ? this.allCategories : Object.values(this.allCategories);
                                        return cats.filter(c => 
                                            c.name.toLowerCase().includes(s) && 
                                            !($wire.category_ids || []).some(id => String(id) === String(c.id))
                                        );
                                    },
                                    add(cat) {
                                        let ids = $wire.category_ids || [];
                                        if (!ids.some(id => String(id) === String(cat.id))) {
                                            $wire.category_ids = [...ids, cat.id];
                                        }
                                        this.search = '';
                                        $refs.searchInput.focus();
                                    },
                                    addFromSearch() {
                                        let s = this.search.trim();
                                        if (!s) return;
                                        
                                        let cats = Array.isArray(this.allCategories) ? this.allCategories : Object.values(this.allCategories);
                                        let existing = cats.find(c => c.name.toLowerCase() === s.toLowerCase());
                                        if (existing) {
                                            this.add(existing);
                                        } else {
                                            let ids = $wire.category_ids || [];
                                            if (!ids.some(item => typeof item === 'string' && item.toLowerCase() === s.toLowerCase())) {
                                                $wire.category_ids = [...ids, s];
                                            }
                                            this.search = '';
                                            $refs.searchInput.focus();
                                        }
                                    },
                                    remove(index) {
                                        let arr = [...($wire.category_ids || [])];
                                        arr.splice(index, 1);
                                        $wire.category_ids = arr;
                                    },
                                    handleBackspace() {
                                        if (this.search === '' && ($wire.category_ids || []).length > 0) {
                                            let arr = [...$wire.category_ids];
                                            arr.pop();
                                            $wire.category_ids = arr;
                                        }
                                    }
                                }" class="relative mt-1" @click.away="search = ''">
                                    
                                    <div class="flex flex-wrap items-center gap-2 border border-gray-300 p-2 rounded-md shadow-sm bg-white focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500 cursor-text" @click="$refs.searchInput.focus()">
                                        <template x-for="(cat, index) in selected" :key="index">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                                <span x-text="cat.name"></span>
                                                <button type="button" @click.stop="remove(index)" class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none">
                                                    <span class="sr-only">Remove</span>
                                                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                                                </button>
                                            </span>
                                        </template>
                                        
                                        <input x-ref="searchInput" type="text" x-model="search" @keydown.enter.prevent="addFromSearch()" @keydown.backspace="handleBackspace()" class="flex-1 outline-none min-w-[120px] bg-transparent border-0 ring-0 focus:ring-0 p-0 text-gray-900 sm:text-sm" placeholder="Search or add category...">
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="search.length > 0" x-transition x-cloak class="absolute z-10 w-full mt-1 bg-white shadow-lg border border-gray-200 max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm">
                                        <template x-if="filtered.length > 0">
                                            <template x-for="cat in filtered" :key="cat.id">
                                                <div @click="add(cat)" class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 text-gray-900 group">
                                                    <span x-text="cat.name" class="block truncate font-medium text-indigo-600"></span>
                                                </div>
                                            </template>
                                        </template>
                                        
                                        <template x-if="filtered.length === 0">
                                            <div @click="addFromSearch()" class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 text-gray-900">
                                                <span class="block truncate text-sm">Create new category: "<span x-text="search" class="font-bold text-indigo-600"></span>"</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <x-input-error :messages="$errors->get('category_ids')" class="mt-2" />
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

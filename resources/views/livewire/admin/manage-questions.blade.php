<div x-data="{
    showForm: false,
    managingOptionsFor: null,
    currentOptions: [],
    optionsError: '',
    openCreate() {
        this.showForm = true;
        // Limpiamos los campos en el navegador, sin esperar a Livewire
        $wire.questionId = null;
        $wire.question_text = '';
        $wire.code_snippet = '';
        $wire.answer_explanation = '';
        $wire.more_info_link = '';
        $wire.type = 'unique-answer';
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
        $wire.type = question.type;
        $wire.category_ids = question.categories ? question.categories.map(c => c.id) : [];
    },
    openOptions(question) {
        this.optionsError = '';
        this.managingOptionsFor = question;
        let opts = question.options ? JSON.parse(JSON.stringify(question.options)) : [];
        if (question.type === 'single-line' || question.type === 'multi-line') {
            if (opts.length === 0) {
                opts.push({ id: null, text: '', correct: true });
            } else {
                opts[0].correct = true;
                opts = [opts[0]];
            }
        } else {
            if (opts.length === 0) {
                opts.push({ id: null, text: '', correct: false });
            }
        }
        this.currentOptions = opts;
        $dispatch('open-modal', 'manage-options-modal');
    },
    addOption() {
        if (this.currentOptions.length < 4) {
            this.currentOptions.push({ id: null, text: '', correct: false });
        }
    },
    removeOption(index) {
        this.currentOptions.splice(index, 1);
    },
    setUniqueCorrect(index) {
        this.currentOptions.forEach((opt, i) => {
            opt.correct = (i === index);
        });
    },
    saveOptions() {
        this.optionsError = '';
        if (this.managingOptionsFor && ['unique-answer', 'multi-answer'].includes(this.managingOptionsFor.type)) {
            if (!this.currentOptions.some(opt => opt.correct)) {
                this.optionsError = 'You must select at least one correct answer.';
                return;
            }
        }
        $wire.saveOptions(this.managingOptionsFor.id, this.currentOptions);
    }
}" @question-saved.window="showForm = false" @options-saved.window="$dispatch('close-modal', 'manage-options-modal');" x-cloak>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
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
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ Str::title(str_replace('-', ' ', $question->type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $question->categories->isNotEmpty() ? $question->categories->pluck('name')->join(', ') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <!-- Cero llamadas a Livewire para rellenar datos. Los inyectamos en JS -->
                                            <button @click="openEdit(@js($question))" type="button" class="text-indigo-600 hover:text-indigo-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                                Edit
                                            </button>
                                            <button @click="openOptions(@js($question))" type="button" class="text-green-600 hover:text-green-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                                Answers
                                            </button>
                                            <button x-data x-on:click="$dispatch('open-delete-modal', {{ $question->id }})" class="text-red-600 hover:text-red-900 focus:outline-none transition duration-150 ease-in-out">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">No questions found.</td>
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
                                <x-input-label for="type" value="Question Type" />
                                <select wire:model="type" id="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="unique-answer">Unique Answer</option>
                                    <option value="multi-answer">Multiple Answer</option>
                                    <option value="single-line">Single Line (Text)</option>
                                    <option value="multi-line">Multi Line (Textarea)</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
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

    <!-- Manage Options / Answers Modal -->
    <x-modal name="manage-options-modal" focusable maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Manage Answers
            </h2>
            
            <template x-if="managingOptionsFor">
                <div class="mb-4 p-4 bg-gray-50 rounded-lg shadow-inner">
                    <p class="text-sm font-medium text-gray-700">Question:</p>
                    <p class="text-sm text-gray-900 mt-1 line-clamp-2" x-text="managingOptionsFor.question_text"></p>
                    <p class="text-xs text-gray-500 mt-2">
                        Type: <span class="font-semibold text-gray-700" x-text="managingOptionsFor.type.replace('-', ' ')"></span>
                    </p>
                </div>
            </template>

            <div x-show="optionsError" x-transition x-cloak class="mb-4 text-sm font-medium text-red-600 bg-red-50 p-3 rounded-md border border-red-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span x-text="optionsError"></span>
            </div>

            <div class="space-y-4">
                <template x-if="managingOptionsFor && managingOptionsFor.type === 'unique-answer'">
                    <div>
                        <p class="text-sm text-gray-600 mb-3">Add up to 4 answers and select the correct one.</p>
                        <template x-for="(option, index) in currentOptions" :key="index">
                            <div class="flex items-center gap-3 mb-3">
                                <input type="radio" :name="'correct_option'" :checked="option.correct" @change="setUniqueCorrect(index)" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500">
                                <x-text-input x-model="option.text" type="text" class="flex-1" placeholder="Answer option text..." />
                                <button type="button" @click="removeOption(index)" class="text-red-500 hover:text-red-700" title="Remove Option">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <x-secondary-button @click="addOption()" x-show="currentOptions.length < 4" type="button" class="mt-2 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Answer
                        </x-secondary-button>
                    </div>
                </template>

                <template x-if="managingOptionsFor && managingOptionsFor.type === 'multi-answer'">
                    <div>
                        <p class="text-sm text-gray-600 mb-3">Add up to 4 answers and select all correct ones.</p>
                        <template x-for="(option, index) in currentOptions" :key="index">
                            <div class="flex items-center gap-3 mb-3">
                                <input type="checkbox" x-model="option.correct" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                <x-text-input x-model="option.text" type="text" class="flex-1" placeholder="Answer option text..." />
                                <button type="button" @click="removeOption(index)" class="text-red-500 hover:text-red-700" title="Remove Option">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <x-secondary-button @click="addOption()" x-show="currentOptions.length < 4" type="button" class="mt-2 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Answer
                        </x-secondary-button>
                    </div>
                </template>

                <template x-if="managingOptionsFor && managingOptionsFor.type === 'single-line'">
                    <div>
                        <p class="text-sm text-gray-600 mb-3">Enter the expected correct answer.</p>
                        <x-text-input x-model="currentOptions[0].text" type="text" class="w-full" placeholder="Correct exact answer..." />
                    </div>
                </template>

                <template x-if="managingOptionsFor && managingOptionsFor.type === 'multi-line'">
                    <div>
                        <p class="text-sm text-gray-600 mb-3">Enter the expected correct answer.</p>
                        <textarea x-model="currentOptions[0].text" rows="4" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Correct exact answer..."></textarea>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button @click="saveOptions()" class="ms-3" type="button" wire:loading.attr="disabled" wire:target="saveOptions">
                    <span wire:loading.remove wire:target="saveOptions">{{ __('Save Answers') }}</span>
                    <span wire:loading wire:target="saveOptions">{{ __('Saving...') }}</span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>

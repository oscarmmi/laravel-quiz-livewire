<div x-data="{
    showForm: false,
    managingQuestionsFor: null,
    selectedQuestions: [],
    openCreate() {
        this.showForm = true;
        $wire.quizId = null;
        $wire.title = '';
        $wire.slug = '';
        $wire.description = '';
        $wire.public = false;
        $wire.published = false;
    },
    openEdit(quiz) {
        this.showForm = true;
        $wire.quizId = quiz.id;
        $wire.title = quiz.title;
        $wire.slug = quiz.slug;
        $wire.description = quiz.description;
        $wire.public = !!quiz.public;
        $wire.published = !!quiz.published;
    },
    openManageQuestions(quiz) {
        this.managingQuestionsFor = quiz;
        this.selectedQuestions = quiz.questions ? quiz.questions.map(q => q.id) : [];
        $dispatch('open-modal', 'manage-quiz-questions');
    },
    saveQuestions() {
        $wire.saveQuestions(this.managingQuestionsFor.id, this.selectedQuestions);
    }
}" @quiz-saved.window="showForm = false" @quiz-questions-saved.window="$dispatch('close-modal', 'manage-quiz-questions');" x-cloak>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Quizzes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- List View -->
            <div x-show="!showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search quizzes..." class="w-full sm:w-1/2" />
                        
                        <x-primary-button @click="openCreate()" type="button">
                            {{ __('Add New Quiz') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Questions</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($quizzes as $quiz)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $quiz->title }}
                                            <div class="text-xs text-gray-400 font-normal">/{{ $quiz->slug }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $quiz->public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $quiz->public ? 'Public' : 'Private' }}
                                            </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $quiz->published ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }} ml-1">
                                                {{ $quiz->published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $quiz->questions_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openManageQuestions(@js($quiz))" type="button" class="text-green-600 hover:text-green-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                                Questions
                                            </button>
                                            <button @click="openEdit(@js($quiz))" type="button" class="text-indigo-600 hover:text-indigo-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                                Edit
                                            </button>
                                            <button x-data x-on:click="$dispatch('open-delete-modal', {{ $quiz->id }})" class="text-red-600 hover:text-red-900 focus:outline-none transition duration-150 ease-in-out">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">No quizzes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($quizzes->hasPages())
                            <div class="p-4 border-t border-gray-200">
                                {{ $quizzes->links() }}
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
                            <span x-text="$wire.quizId ? 'Edit Quiz' : 'Create New Quiz'"></span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="title" value="Title" />
                                <x-text-input wire:model="title" id="title" type="text" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="slug" value="Slug (Auto-generates if empty)" />
                                <x-text-input wire:model="slug" id="slug" type="text" class="mt-1 block w-full text-sm font-mono" />
                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="description" value="Description" />
                                <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-6 mt-4">
                                <label for="public" class="inline-flex items-center cursor-pointer">
                                    <input id="public" type="checkbox" wire:model="public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Public') }}</span>
                                </label>

                                <label for="published" class="inline-flex items-center cursor-pointer">
                                    <input id="published" type="checkbox" wire:model="published" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Published') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button @click="showForm = false" type="button">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">{{ __('Save Quiz') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal Alpine Wrapper -->
    <div x-data="{ quizIdToDelete: null }" @open-delete-modal.window="quizIdToDelete = $event.detail; $dispatch('open-modal', 'confirm-quiz-delete')">
        <x-modal name="confirm-quiz-delete" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this quiz?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    This action is permanent and cannot be undone. Associated questions will not be deleted from the database, but will be structurally disconnected from this quiz.
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button x-on:click="$wire.delete(quizIdToDelete)" class="ms-3">
                        {{ __('Yes, Delete Quiz') }}
                    </x-danger-button>
                </div>
            </div>
        </x-modal>
    </div>
    <!-- Manage Quiz Questions Modal -->
    <x-modal name="manage-quiz-questions" focusable maxWidth="2xl">
        <div class="p-6" x-data="{
            activeTab: 'questions',
            qSearch: '',
            cSearch: '',
            get allQ() { return @js($allQuestions); },
            get allC() { return @js($categories); },
            
            get filteredQuestions() {
                if (this.qSearch.trim() === '') return this.allQ.filter(q => !selectedQuestions.includes(q.id));
                let s = this.qSearch.toLowerCase();
                return this.allQ.filter(q => q.question_text.toLowerCase().includes(s) && !selectedQuestions.includes(q.id));
            },
            
            get filteredCategories() {
                if (this.cSearch.trim() === '') return this.allC;
                let s = this.cSearch.toLowerCase();
                return this.allC.filter(c => c.name.toLowerCase().includes(s));
            },
            
            addQuestion(q) {
                if (!selectedQuestions.includes(q.id)) {
                    selectedQuestions.push(q.id);
                }
                this.qSearch = ''; 
            },
            
            removeQuestion(id) {
                selectedQuestions = selectedQuestions.filter(i => i !== id);
            },
            
            addByCategory(catId) {
                let qs = this.allQ.filter(q => {
                    return q.categories && q.categories.some(c => c.id === catId);
                });
                
                qs.forEach(q => {
                    if (!selectedQuestions.includes(q.id)) {
                        selectedQuestions.push(q.id);
                    }
                });
            },
            
            get selectedQuestionObjects() {
                return selectedQuestions.map(id => this.allQ.find(q => q.id === id)).filter(Boolean);
            }
        }">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                Manage Questions
            </h2>
            <span x-show="managingQuestionsFor" class="text-indigo-600 border-l border-gray-300 ml-3 pl-3 text-sm flex-1 truncate" x-text="managingQuestionsFor?.title"></span>
            <br>
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-4 mt-4">
                <button @click="activeTab = 'questions'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'questions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'questions'}" class="whitespace-nowrap pb-2 px-4 border-b-2 font-medium text-sm">
                    Select Questions
                </button>
                <button @click="activeTab = 'categories'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'categories', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'categories'}" class="whitespace-nowrap pb-2 px-4 border-b-2 font-medium text-sm">
                    Add by Category
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column: Search & Add -->
                <div>
                    <!-- Question Search Tab -->
                    <div x-show="activeTab === 'questions'" class="space-y-4">
                        <div class="relative">
                            <x-input-label value="Search and Add Question" />
                            <x-text-input x-model="qSearch" class="mt-1 block w-full" placeholder="Type to filter..." />
                            
                            <div class="mt-2 border border-gray-200 rounded-md bg-white max-h-60 overflow-y-auto">
                                <template x-for="q in filteredQuestions" :key="q.id">
                                    <div @click="addQuestion(q)" class="flex items-center justify-between cursor-pointer py-2 px-3 border-b border-gray-100 last:border-b-0 hover:bg-indigo-50 group">
                                        <span x-text="q.question_text" class="text-sm text-gray-700 line-clamp-2"></span>
                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-500 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </template>
                                <template x-if="filteredQuestions.length === 0">
                                    <div class="py-3 px-4 text-sm text-gray-500 text-center">No questions found.</div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Category Add Tab -->
                    <div x-show="activeTab === 'categories'" class="space-y-4" x-cloak>
                        <div class="relative">
                            <x-input-label value="Filter Categories" />
                            <x-text-input x-model="cSearch" class="mt-1 block w-full mb-2" placeholder="Type to filter..." />
                            <p class="text-xs text-gray-500 mb-2">Clicking a category adds all its questions to this quiz.</p>
                            
                            <div class="grid grid-cols-1 gap-2 max-h-56 overflow-y-auto p-1">
                                <template x-for="cat in filteredCategories" :key="cat.id">
                                    <button @click="addByCategory(cat.id)" type="button" class="flex items-center justify-between px-3 py-2 bg-white border border-gray-300 rounded shadow-sm hover:bg-indigo-50 text-left text-sm transition-colors group">
                                        <span x-text="cat.name" class="truncate font-medium text-gray-700 group-hover:text-indigo-700"></span>
                                        <svg class="w-4 h-4 text-indigo-400 opacity-50 group-hover:opacity-100 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </template>
                                <template x-if="filteredCategories.length === 0">
                                    <div class="col-span-full py-4 text-center text-gray-500 text-sm">No categories found.</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Selected List -->
                <div class="bg-gray-50 p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-3 border-b border-gray-200 pb-2">
                        <h3 class="text-sm font-semibold text-gray-800">Selected Questions</h3>
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-indigo-100 bg-indigo-600 rounded-full" x-text="selectedQuestions.length"></span>
                    </div>
                    
                    <div class="overflow-y-auto pr-1" style="max-height: 280px;">
                        <ul class="space-y-2">
                            <template x-for="(q, index) in selectedQuestionObjects" :key="q.id">
                                <li class="bg-white px-3 py-2 border border-gray-200 shadow-sm rounded flex items-start justify-between hover:border-red-300 transition-colors">
                                    <div class="flex flex-col pr-2">
                                        <span class="text-xs text-gray-400 font-medium mb-1" x-text="'#' + (index + 1)"></span>
                                        <span class="text-sm text-gray-700 line-clamp-2" x-text="q.question_text"></span>
                                    </div>
                                    <button @click="removeQuestion(q.id)" type="button" class="text-gray-400 hover:text-red-500 flex-shrink-0 transition-colors" title="Remove">
                                        <svg class="h-5 w-5 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </li>
                            </template>
                            <template x-if="selectedQuestionObjects.length === 0">
                                <li class="py-8 text-center text-sm text-gray-500 border border-dashed border-gray-300 rounded">No questions selected.</li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Close') }}
                </x-secondary-button>

                <x-primary-button @click="saveQuestions()" class="ms-3" type="button" wire:loading.attr="disabled" wire:target="saveQuestions">
                    <span wire:loading.remove wire:target="saveQuestions">{{ __('Save Answers') }}</span>
                    <span wire:loading wire:target="saveQuestions">{{ __('Saving...') }}</span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>

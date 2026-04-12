<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Quizzes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(!$isFormOpen)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search quizzes..." class="w-full sm:w-1/2" />
                        <x-primary-button wire:click="create" wire:loading.attr="disabled" wire:target="create">
                            <span wire:loading.remove wire:target="create">
                                {{ __('Add New Quiz') }}
                            </span>
                            <span wire:loading wire:target="create">
                                {{ __('Loading...') }}
                            </span>
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
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
                                            <button wire:click="edit({{ $quiz->id }})" class="text-indigo-600 hover:text-indigo-900 focus:outline-none transition duration-150 ease-in-out mr-4">
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
            @else
                <!-- Form View -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form wire:submit.prevent="save" class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $quizId ? 'Edit Quiz' : 'Create New Quiz' }}
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
                            <x-secondary-button wire:click="closeForm" wire:loading.attr="disabled" wire:target="closeForm">
                                <span wire:loading.remove wire:target="closeForm">{{ __('Cancel') }}</span>
                                <span wire:loading wire:target="closeForm">{{ __('Cancelling...') }}</span>
                            </x-secondary-button>

                            <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">{{ __('Save Quiz') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
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
</div>

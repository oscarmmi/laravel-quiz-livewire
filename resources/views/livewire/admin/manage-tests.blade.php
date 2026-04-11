<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Tests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search by user or quiz..." class="w-full sm:w-1/2" />
                    <x-primary-button wire:click="create">
                        {{ __('Add New Test Record') }}
                    </x-primary-button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tests as $test)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $test->user ? $test->user->name : 'Deleted User' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $test->quiz ? $test->quiz->title : 'Deleted Quiz' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold {{ $test->result > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $test->result }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ $test->time_spent ? $test->time_spent . 's' : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $test->id }})" class="text-indigo-600 hover:text-indigo-900 focus:outline-none transition duration-150 ease-in-out mr-4">
                                            Edit
                                        </button>
                                        <button x-data x-on:click="$dispatch('open-delete-modal', {{ $test->id }})" class="text-red-600 hover:text-red-900 focus:outline-none transition duration-150 ease-in-out">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No test records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if($tests->hasPages())
                        <div class="p-4 border-t border-gray-200">
                            {{ $tests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    <x-modal name="test-form" focusable maxWidth="2xl">
        <form wire:submit.prevent="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ $testId ? 'Edit Test Record' : 'Create New Test Record' }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="user_id" value="User" />
                    <select wire:model="user_id" id="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select a User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="quiz_id" value="Quiz" />
                    <select wire:model="quiz_id" id="quiz_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select a Quiz</option>
                        @foreach($quizzes as $quiz)
                            <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('quiz_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="result" value="Score / Result" />
                    <x-text-input wire:model="result" id="result" type="number" min="0" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('result')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="time_spent" value="Time Spent (seconds) - Optional" />
                    <x-text-input wire:model="time_spent" id="time_spent" type="number" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('time_spent')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="ip_address" value="IP Address - Optional" />
                    <x-text-input wire:model="ip_address" id="ip_address" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('ip_address')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save Test Record') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal Alpine Wrapper -->
    <div x-data="{ testIdToDelete: null }" @open-delete-modal.window="testIdToDelete = $event.detail; $dispatch('open-modal', 'confirm-test-delete')">
        <x-modal name="confirm-test-delete" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this test record?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    This action is permanent. This will remove this result from the leaderboard entirely.
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button x-on:click="$wire.delete(testIdToDelete)" class="ms-3">
                        {{ __('Yes, Delete Record') }}
                    </x-danger-button>
                </div>
            </div>
        </x-modal>
    </div>
</div>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Administrators') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search users by name or email..." class="w-full sm:w-1/2" />
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($user->is_admin)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Admin
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->id() !== $user->id)
                                            <button x-data x-on:click="$dispatch('open-confirm-modal', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', is_admin: {{ $user->is_admin ? 'true' : 'false' }} })" class="text-indigo-600 hover:text-indigo-900 font-semibold focus:outline-none focus:underline transition duration-150 ease-in-out">
                                                {{ $user->is_admin ? 'Revoke Admin' : 'Make Admin' }}
                                            </button>
                                        @else
                                            <span class="text-gray-400 italic">It's You</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if($users->hasPages())
                        <div class="p-4 border-t border-gray-200">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-data="{
        userId: null,
        userName: '',
        isAdmin: false
    }" @open-confirm-modal.window="userId = $event.detail.id; userName = $event.detail.name; isAdmin = $event.detail.is_admin; $dispatch('open-modal', 'confirm-user-toggle')">

        <x-modal name="confirm-user-toggle" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to <span x-text="isAdmin ? 'revoke' : 'grant'"></span> admin access?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    You are about to modify the privileges for <strong class="text-gray-900" x-text="userName"></strong>.
                    
                    <span x-show="isAdmin">
                        They will immediately lose access to all admin-only pages such as this one.
                    </span>
                    <span x-show="!isAdmin" style="display: none;">
                        They will be granted full access to administrative features across the system.
                    </span>
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button x-cloak x-show="isAdmin" x-on:click="$wire.toggleAdmin(userId); $dispatch('close')" class="ms-3">
                        {{ __('Yes, Revoke Admin') }}
                    </x-danger-button>
                    
                    <x-primary-button x-cloak x-show="!isAdmin" x-on:click="$wire.toggleAdmin(userId); $dispatch('close')" class="ms-3">
                        {{ __('Yes, Make Admin') }}
                    </x-primary-button>
                </div>
            </div>
        </x-modal>
    </div>
</div>

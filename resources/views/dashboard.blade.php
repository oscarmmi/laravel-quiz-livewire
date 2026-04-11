<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <h3 class="text-2xl font-bold text-gray-800 mb-6 px-2 sm:px-0">Available Quizzes</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($quizzes as $quiz)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col hover:shadow-md transition-shadow">
                        <div class="p-6 flex-1 border-b border-gray-100">
                            <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $quiz->title }}</h4>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $quiz->description }}</p>
                            
                            <div class="inline-flex items-center text-xs text-indigo-600 font-semibold bg-indigo-50 px-2.5 py-1 rounded-full">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $quiz->questions_count }} {{ Str::plural('Question', $quiz->questions_count) }}
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 mt-auto flex justify-end">
                            <a href="#" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                Take Quiz 
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center border-2 border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <h3 class="text-sm font-semibold text-gray-900">No Quizzes Available</h3>
                        <p class="mt-1 text-sm text-gray-500">There are currently no public quizzes available to take.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

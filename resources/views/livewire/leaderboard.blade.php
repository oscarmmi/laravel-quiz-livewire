<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leaderboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 sm:mb-0">Score Rankings</h3>
                        
                        <!-- Filter -->
                        <div class="w-full sm:w-1/3">
                            <select wire:model.live="quiz_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                                <option value="0">All Quizzes</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto border border-gray-100 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User Name</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Score</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Time (s)</th>
                                        </tr>
                                    </thead>
<tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($tests as $index => $test)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    @if($index === 0)
                                                        <span class="text-yellow-500">🏆 #1</span>
                                                    @elseif($index === 1)
                                                        <span class="text-gray-400">🥈 #2</span>
                                                    @elseif($index === 2)
                                                        <span class="text-amber-600">🥉 #3</span>
                                                    @else
                                                        #{{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->user->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 text-center">{{ number_format($test->avg_result, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $test->total_time_spent }}s</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No leaderboard rankings found for this selection.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

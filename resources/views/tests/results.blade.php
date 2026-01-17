<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Results: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $test->title }} - Results</h3>
                    <div class="mb-4">
                        <p class="text-md text-gray-800">Your Score: <span class="font-bold">{{ $userTest->score }}</span> / {{ $totalQuestions }}</p>
                        <p class="text-sm text-gray-600">Completed at: {{ $userTest->completed_at->format('M d, Y H:i A') }}</p>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('tests.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Back to Tests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
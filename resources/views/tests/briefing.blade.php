<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Briefing: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $test->title }}</h3>
                    <div class="prose max-w-none mb-6">
                        <p><strong>Duration:</strong> {{ $test->duration }} minutes</p>
                        <p><strong>Instructions:</strong></p>
                        {!! nl2br(e($test->description)) !!}
                    </div>

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex items-center space-x-4">
                        @if($isCompleted)
                            {{-- Tampilkan tombol Hasil Test jika sudah dikerjakan --}}
                            <a href="{{ route('tests.results', $test->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                Lihat Hasil Test
                            </a>
                        @else
                            {{-- Form Mulai Test --}}
                            <form action="{{ route('tests.start', $test->id) }}" method="POST" class="flex flex-col md:flex-row md:items-center gap-4">
                                @csrf
                                
                                @if($test->token)
                                    <div class="flex items-center gap-2">
                                        <label for="token" class="text-sm font-bold text-gray-700 uppercase">Token:</label>
                                        <x-input type="text" name="token" id="token" class="w-32 uppercase tracking-widest text-center" placeholder="******" required />
                                    </div>
                                @endif

                                <x-button class="bg-green-600 hover:bg-green-700 ring-green-300">
                                    Mulai Test
                                </x-button>
                            </form>
                        @endif

                        {{-- Tombol Kembali ke Daftar Test --}}
                        <a href="{{ route('tests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Daftar Ujian
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

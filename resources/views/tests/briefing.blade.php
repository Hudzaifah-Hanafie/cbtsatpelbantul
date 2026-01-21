<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Briefing: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showStartModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
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

                    <div class="space-y-6">
                        @if($isCompleted)
                            {{-- Tampilkan Hasil jika sudah selesai - Card Full Width --}}
                            <div class="bg-green-50 border border-green-200 rounded-xl p-6 w-full shadow-sm">
                                <h4 class="text-xl font-bold text-green-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Selamat! Kamu telah menyelesaikan ujian ini.
                                </h4>
                                <p class="text-green-700 mb-4">
                                    Selesai pada tanggal <strong>{{ $scoreData['completed_at']->format('d M Y') }}</strong> 
                                    jam <strong>{{ $scoreData['completed_at']->format('H:i') }}</strong>.
                                </p>
                                <div class="inline-block bg-white px-4 py-2 rounded-lg border border-green-200">
                                    <span class="text-gray-600 text-sm uppercase font-bold tracking-wider">Skor Kamu:</span>
                                    <div class="text-3xl font-extrabold text-green-600">
                                        {{ $scoreData['score'] }} <span class="text-gray-400 text-lg font-normal">/ {{ $scoreData['total'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Container Tombol Navigasi --}}
                        <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
                            @if(!$isCompleted)
                                {{-- Form Mulai Test --}}
                                <form id="start-form" action="{{ route('tests.start', $test->id) }}" method="POST" class="w-full md:w-auto flex flex-col md:flex-row md:items-center gap-4">
                                    @csrf
                                    
                                    @if($test->token)
                                        <div class="flex items-center gap-2">
                                            <label for="token" class="text-sm font-bold text-gray-700 uppercase whitespace-nowrap">Token:</label>
                                            <x-input type="text" name="token" id="token" class="w-full md:w-32 uppercase tracking-widest text-center" placeholder="******" required />
                                        </div>
                                    @endif

                                    <button type="button" @click="showStartModal = true" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-lg shadow-md text-base font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform active:scale-95">
                                        Mulai Test
                                    </button>
                                </form>
                            @endif

                            {{-- Tombol Kembali ke Daftar Test - md:ml-auto agar mepet kanan di desktop --}}
                            <a href="{{ route('tests.index') }}" class="w-full md:w-auto md:ml-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Daftar Ujian
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Start -->
        <div x-show="showStartModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showStartModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showStartModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Siap Memulai Ujian?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Pastikan Anda sudah siap. Waktu akan langsung berjalan begitu Anda menekan tombol mulai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" onclick="document.getElementById('start-form').submit()" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Ya, Mulai Sekarang
                            </button>
                            <button type="button" @click="showStartModal = false" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
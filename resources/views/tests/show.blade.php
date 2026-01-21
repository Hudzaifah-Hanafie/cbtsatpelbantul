<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showFinishModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Area Utama Soal -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Question {{ $questionNumber }} of {{ $totalQuestions }}</h3>
                            </div>

                            <form id="test-form" action="{{ route('tests.saveAnswer', ['test' => $test->id, 'questionNumber' => $questionNumber]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="jump_to" id="jump_to_input">
                                <input type="hidden" name="finish" id="finish_input" value="0">
                                
                                <div>
                                    <p class="text-md text-gray-800 mb-4">{{ $currentQuestion->question_text }}</p>
                                    <div class="flex flex-col mt-4 space-y-2">
                                        @foreach($currentQuestion->options as $option)
                                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-blue-50 transition">
                                                <input type="radio" name="question_{{ $currentQuestion->id }}" value="{{ $option->id }}" class="form-radio"
                                                {{ (isset($existingAnswer) && $existingAnswer->option_id == $option->id) ? 'checked' : '' }}>
                                                <span class="ml-2 text-gray-700">{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    @if ($questionNumber > 1)
                                        <button type="submit" name="previous" value="1" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Previous
                                        </button>
                                    @else
                                        <span></span> {{-- Placeholder to maintain space --}}
                                    @endif

                                    @if ($questionNumber < $totalQuestions)
                                        <button type="submit" name="next" value="1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Next
                                        </button>
                                    @else
                                        {{-- Tombol Finish memicu Modal --}}
                                        <button type="button" @click="showFinishModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Finish Test
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Panel Kanan -->
                <div class="w-full md:w-1/3 space-y-6">
                    
                    <!-- 1. FITUR TIMER -->
                    <div class="bg-white shadow sm:rounded-lg p-4 sticky top-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Sisa Waktu</h3>
                        <div id="timer-display" class="text-3xl font-bold text-center py-2 bg-gray-100 rounded text-gray-800">
                            Loading...
                        </div>
                    </div>

                    <!-- 2. FITUR NAVIGASI PANEL SOAL -->
                    <div class="bg-white shadow sm:rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Navigasi Soal</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($questions as $index => $q)
                                @php 
                                    $qNum = $index + 1;
                                    // Cek apakah soal ini ada di daftar yang sudah dijawab
                                    $isAnswered = in_array($q->id, $answeredQuestions);
                                    $isCurrent = $qNum == $questionNumber;
                                @endphp

                                <button type="button" onclick="goToQuestion({{ $qNum }})"
                                   class="flex items-center justify-center w-10 h-10 rounded text-sm font-semibold transition-colors duration-200
                                   {{ $isCurrent ? 'ring-2 ring-indigo-500 border-indigo-500' : '' }}
                                   {{ $isAnswered ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $qNum }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Finish -->
        <div x-show="showFinishModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showFinishModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showFinishModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Kumpulkan Jawaban?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Apakah Anda yakin ingin menyelesaikan ujian ini? Pastikan Anda sudah menjawab semua soal. Setelah dikumpulkan, Anda tidak dapat mengubah jawaban lagi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" onclick="submitFinish()" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Ya, Kumpulkan
                            </button>
                            <button type="button" @click="showFinishModal = false" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Form Submit Hidden untuk Auto-Submit Timer -->
    <form id="auto-submit-form" action="{{ route('tests.submit', $test->id) }}" method="POST" class="hidden">
        @csrf
    </form>

    @push('scripts')
        <script>
            /**
             * Submit Finish Logic
             */
            function submitFinish() {
                const form = document.getElementById('test-form');
                const finishInput = document.getElementById('finish_input');
                finishInput.value = '1'; // Tandai sebagai finish action
                form.submit();
            }

            /**
             * Navigasi Grid Logic
             */
            function goToQuestion(number) {
                const form = document.getElementById('test-form');
                const jumpInput = document.getElementById('jump_to_input');
                jumpInput.value = number;
                form.submit();
            }

            /**
             * Modul Timer Ujian
             */
            const ExamTimer = {
                timerId: null,
                displayElement: document.getElementById('timer-display'),
                submitForm: document.getElementById('auto-submit-form'),
                WARNING_THRESHOLD: 5 * 60, 

                init: function(serverRemainingSeconds) {
                    console.log("Initializing Timer with server seconds:", serverRemainingSeconds);
                    let remainingSeconds = serverRemainingSeconds;

                    try {
                        const savedEndTime = localStorage.getItem('exam_end_time');
                        const now = Math.floor(Date.now() / 1000);

                        if (savedEndTime) {
                            remainingSeconds = parseInt(savedEndTime) - now;
                            
                            if (Math.abs(remainingSeconds - serverRemainingSeconds) > 30) {
                                console.warn("Time drift detected! Resyncing with server.");
                                remainingSeconds = serverRemainingSeconds;
                                localStorage.setItem('exam_end_time', now + remainingSeconds);
                            }
                        } else {
                            const targetTime = now + serverRemainingSeconds;
                            localStorage.setItem('exam_end_time', targetTime);
                        }
                    } catch (e) {
                        console.error("Timer Error:", e);
                        remainingSeconds = serverRemainingSeconds;
                    }

                    this.tick(remainingSeconds);
                    this.timerId = setInterval(() => {
                        remainingSeconds--;
                        this.tick(remainingSeconds);
                    }, 1000);
                },

                tick: function(secondsLeft) {
                    if (secondsLeft <= 0) {
                        clearInterval(this.timerId);
                        this.displayElement.innerText = "00:00:00";
                        this.displayElement.classList.add('text-red-600');
                        
                        if (!this.submitted) {
                            this.submitted = true;
                            alert('Waktu habis! Jawaban Anda akan dikirim otomatis.');
                            localStorage.removeItem('exam_end_time');
                            this.submitForm.submit();
                        }
                        return;
                    }

                    if (secondsLeft <= this.WARNING_THRESHOLD) {
                        this.displayElement.classList.remove('text-gray-800');
                        this.displayElement.classList.add('text-red-600', 'animate-pulse');
                    } else {
                        if (this.displayElement.classList.contains('text-red-600')) {
                             this.displayElement.classList.remove('text-red-600', 'animate-pulse');
                             this.displayElement.classList.add('text-gray-800');
                        }
                    }

                    this.displayElement.innerText = this.formatTime(secondsLeft);
                },

                formatTime: function(seconds) {
                    const h = Math.floor(seconds / 3600);
                    const m = Math.floor((seconds % 3600) / 60);
                    const s = seconds % 60;
                    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                const serverTime = {{ (int) $remainingSeconds }};
                ExamTimer.init(serverTime);
            });
        </script>
    @endpush
</x-app-layout>
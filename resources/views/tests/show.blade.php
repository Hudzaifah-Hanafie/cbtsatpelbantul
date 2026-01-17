<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
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
                                        <button type="submit" name="finish" value="1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
    </div>

    <!-- Form Submit Hidden untuk Auto-Submit Timer -->
    <form id="auto-submit-form" action="{{ route('tests.submit', $test->id) }}" method="POST" class="hidden">
        @csrf
    </form>

    @push('scripts')
        <script>
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
                            console.log("Found cached end time. Remaining:", remainingSeconds);
                            
                            // Validasi drastis: jika client time jauh lebih lama dari server, pakai server
                            if (Math.abs(remainingSeconds - serverRemainingSeconds) > 30) {
                                console.warn("Time drift detected! Resyncing with server.");
                                remainingSeconds = serverRemainingSeconds;
                                localStorage.setItem('exam_end_time', now + remainingSeconds);
                            }
                        } else {
                            console.log("No cache found. Setting new end time.");
                            const targetTime = now + serverRemainingSeconds;
                            localStorage.setItem('exam_end_time', targetTime);
                        }
                    } catch (e) {
                        console.error("Timer Error:", e);
                        // Fallback ke server time jika localStorage bermasalah
                        remainingSeconds = serverRemainingSeconds;
                    }

                    // Start Loop
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
                // Ensure the value is passed as an integer literal
                const serverTime = {{ (int) $remainingSeconds }};
                ExamTimer.init(serverTime);
            });
        </script>
    @endpush
</x-app-layout>

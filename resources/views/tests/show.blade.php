<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center md:text-left">
            {{ __('Ujian: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showFinishModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Area Utama Soal -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg">PERTANYAAN {{ $questionNumber }} dari {{ $totalQuestions }}</h3>
                            </div>

                            <form id="test-form" action="{{ route('tests.saveAnswer', ['test' => $test->id, 'questionNumber' => $questionNumber]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="jump_to" id="jump_to_input">
                                <input type="hidden" name="finish" id="finish_input" value="0">
                                
                                <div class="space-y-6">
                                    <!-- Render Gambar Soal -->
                                    @if($currentQuestion->image_path)
                                        <div class="mb-4">
                                            <img src="{{ asset('storage/' . $currentQuestion->image_path) }}" alt="Gambar Soal" class="max-w-full h-auto rounded-xl shadow-sm border border-gray-100 mx-auto">
                                        </div>
                                    @endif

                                    <p class="text-lg text-gray-800 font-medium leading-relaxed">{{ $currentQuestion->question_text }}</p>
                                    
                                    <div class="flex flex-col mt-6 space-y-3">
                                        @foreach($currentQuestion->options as $index => $option)
                                            <label class="flex items-start cursor-pointer p-4 border-2 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group {{ (isset($existingAnswer) && $existingAnswer->option_id == $option->id) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100' }}">
                                                <div class="flex items-center h-5 pt-1">
                                                    <input type="radio" name="question_{{ $currentQuestion->id }}" value="{{ $option->id }}" class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 cursor-pointer"
                                                    {{ (isset($existingAnswer) && $existingAnswer->option_id == $option->id) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <!-- Render Gambar Opsi -->
                                                    @if($option->image_path)
                                                        <div class="mb-3">
                                                            <img src="{{ asset('storage/' . $option->image_path) }}" alt="Gambar Opsi" class="max-h-48 rounded-lg shadow-sm border border-gray-200">
                                                        </div>
                                                    @endif
                                                    <span class="text-gray-700 font-semibold text-md group-hover:text-indigo-900 transition-colors">
                                                        {{ chr(65 + $index) }}. {{ $option->option_text }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between gap-4">
                                    <div class="flex gap-3">
                                        @if ($questionNumber > 1)
                                            <button type="submit" name="previous" value="1" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2 border border-gray-300 text-sm font-bold rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                                SEBELUMNYA
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex gap-3">
                                        @if ($questionNumber < $totalQuestions)
                                            <button type="submit" name="next" value="1" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all uppercase tracking-widest">
                                                SELANJUTNYA
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" @click="showFinishModal = true" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all uppercase tracking-widest">
                                                SELESAI & KUMPULKAN
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Panel Kanan -->
                <div class="w-full md:w-1/3 space-y-6">
                    
                    <!-- Timer -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200 sticky top-4">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3">Sisa Waktu</h3>
                        <div id="timer-display" class="text-4xl font-extrabold text-center py-4 bg-gray-50 rounded-xl text-gray-800 tabular-nums border border-gray-100">
                            --:--
                        </div>
                    </div>

                    <!-- Navigasi Panel Soal -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Navigasi Soal</h3>
                        <div class="grid grid-cols-5 gap-3">
                            @foreach($questions as $index => $q)
                                @php 
                                    $qNum = $index + 1;
                                    $isAnswered = in_array($q->id, $answeredQuestions);
                                    $isCurrent = $qNum == $questionNumber;
                                @endphp

                                <button type="button" onclick="goToQuestion({{ $qNum }})"
                                   class="flex items-center justify-center w-10 h-10 rounded-lg text-sm font-bold transition-all duration-200 border-2
                                   {{ $isCurrent ? 'border-indigo-600 bg-white text-indigo-600 shadow-md ring-2 ring-indigo-100' : '' }}
                                   {{ !$isCurrent && $isAnswered ? 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' : '' }}
                                   {{ !$isCurrent && !$isAnswered ? 'bg-gray-50 border-gray-100 text-gray-400 hover:border-indigo-200 hover:text-indigo-400' : '' }}">
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
                                    <h3 class="text-lg font-bold leading-6 text-gray-900 uppercase tracking-tight" id="modal-title">Selesai Mengerjakan?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 leading-relaxed">
                                            Apakah Anda yakin ingin mengumpulkan jawaban sekarang? Anda tidak akan bisa mengubah jawaban lagi setelah ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" onclick="submitFinish()" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-base font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest">
                                Ya, Kumpulkan
                            </button>
                            <button type="button" @click="showFinishModal = false" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest">
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
            function submitFinish() {
                const form = document.getElementById('test-form');
                const finishInput = document.getElementById('finish_input');
                finishInput.value = '1';
                form.submit();
            }

            function goToQuestion(number) {
                const form = document.getElementById('test-form');
                const jumpInput = document.getElementById('jump_to_input');
                jumpInput.value = number;
                form.submit();
            }

            const ExamTimer = {
                timerId: null,
                displayElement: document.getElementById('timer-display'),
                submitForm: document.getElementById('auto-submit-form'),
                WARNING_THRESHOLD: 5 * 60, 

                init: function(serverRemainingSeconds) {
                    let remainingSeconds = serverRemainingSeconds;
                    try {
                        const savedEndTime = localStorage.getItem('exam_end_time');
                        const now = Math.floor(Date.now() / 1000);
                        if (savedEndTime) {
                            remainingSeconds = parseInt(savedEndTime) - now;
                            if (Math.abs(remainingSeconds - serverRemainingSeconds) > 30) {
                                remainingSeconds = serverRemainingSeconds;
                                localStorage.setItem('exam_end_time', now + remainingSeconds);
                            }
                        } else {
                            localStorage.setItem('exam_end_time', now + serverRemainingSeconds);
                        }
                    } catch (e) {
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

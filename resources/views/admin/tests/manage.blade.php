<x-admin-layout>
    <x-slot name="header">
        {{ __('Kelola Soal: ' . $test->title) }}
    </x-slot>

    <!-- 1. Simpan data ke Global Variable dulu agar aman dari conflict quote HTML -->
    <script>
        window.initialQuestionsData = @json($test->questions);
    </script>

    <!-- 2. Panggil data dari variable tersebut -->
    <div x-data="bulkEditor(window.initialQuestionsData)">
        <div class="max-w-7xl mx-auto">
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Ada kesalahan input:</strong>
                    <ul class="list-disc ml-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Section Token (Form Terpisah) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Token Akses Ujian</h3>
                        <p class="text-sm text-gray-500">Token ini digunakan peserta untuk memulai ujian.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 w-full md:w-auto">
                        <form action="{{ route('admin.tests.update_token', $test->id) }}" method="POST" class="flex items-end gap-2">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="token" class="block text-sm font-medium text-gray-700">Token</label>
                                <x-input type="text" name="token" id="token" value="{{ $test->token }}" class="mt-1 block w-32 text-center uppercase tracking-widest font-bold" placeholder="TOKEN" />
                            </div>
                            <x-button class="h-10">
                                Simpan
                            </x-button>
                            <button type="submit" name="generate" value="1" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md h-10">
                                Generate
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Form Utama: Metadata + Soal -->
            <form action="{{ route('admin.tests.update_bulk', $test->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Section Metadata Test (Judul, Deskripsi, Durasi) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Dasar Ujian</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Ujian</label>
                            <x-input type="text" name="title" id="title" value="{{ old('title', $test->title) }}" class="w-full" required />
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Instruksi</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-lg shadow-sm border-gray-300 text-gray-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-2 px-3 leading-tight transition duration-150 ease-in-out">{{ old('description', $test->description) }}</textarea>
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                            <x-input type="number" name="duration" id="duration" value="{{ old('duration', $test->duration) }}" min="1" class="w-full" required />
                        </div>
                    </div>
                </div>

                <!-- Container Soal -->
                <div class="space-y-6">
                    <template x-for="(question, qIndex) in questions" :key="question.temp_id">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 relative">
                            
                            <!-- Header Soal (Nomor & Hapus) -->
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-700" x-text="`Soal No. ${qIndex + 1}`"></h3>
                                <button type="button" @click="removeQuestion(qIndex)" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                                    Hapus Soal
                                </button>
                            </div>

                            <!-- Input Hidden ID (Untuk update existing) -->
                            <input type="hidden" :name="`questions[${qIndex}][id]`" x-model="question.id">

                            <!-- Textarea Soal -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pertanyaan</label>
                                <textarea 
                                    :name="`questions[${qIndex}][question_text]`" 
                                    x-model="question.question_text"
                                    rows="2" 
                                    class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 border-gray-300"
                                    placeholder="Tulis pertanyaan di sini..." required></textarea>
                            </div>

                            <!-- Opsi Jawaban -->
                            <div class="ml-4 pl-4 border-l-2 border-gray-200">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilihan Jawaban</label>
                                
                                <div class="space-y-3">
                                    <template x-for="(option, oIndex) in question.options" :key="option.temp_id">
                                        <div class="flex items-center gap-3">
                                            <!-- Radio Button Benar -->
                                            <input type="radio" 
                                                :name="`questions[${qIndex}][correct_index]`" 
                                                :value="oIndex" 
                                                x-model="question.correct_index"
                                                class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 cursor-pointer" 
                                                title="Tandai sebagai jawaban benar" required>
                                            
                                            <!-- Input Teks Opsi -->
                                            <input type="text" 
                                                :name="`questions[${qIndex}][options][${oIndex}][option_text]`" 
                                                x-model="option.option_text"
                                                class="flex-1 shadow-sm border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-1 focus:ring-indigo-500 border-gray-300" 
                                                placeholder="Tulis opsi jawaban..." required>

                                            <!-- Hapus Opsi -->
                                            <button type="button" @click="removeOption(qIndex, oIndex)" class="text-gray-400 hover:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Tombol Tambah Opsi -->
                                <button type="button" @click="addOption(qIndex)" class="mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Opsi
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Tombol Tambah Soal (Floating Bottom or Static) -->
                <div class="mt-6 flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-inner">
                    <button type="button" @click="addQuestion()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pertanyaan Baru
                    </button>

                    <div class="flex gap-4">
                        <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-800 font-medium py-2">Kembali</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow">
                            Simpan Semua Perubahan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Pastikan function ini ada di Global Scope
        function bulkEditor(initialData) {
            return {
                questions: [],

                init() {
                    console.log('Bulk Editor Initialized with data:', initialData);
                    if (initialData && initialData.length > 0) {
                        this.questions = initialData.map(q => {
                            // Cari index opsi yang benar
                            let correctIndex = q.options.findIndex(o => o.is_correct == 1);
                            if (correctIndex === -1) correctIndex = 0; // Default fallback

                            return {
                                id: q.id,
                                temp_id: Math.random().toString(36).substr(2, 9),
                                question_text: q.question_text,
                                correct_index: correctIndex,
                                options: q.options.map(o => ({
                                    temp_id: Math.random().toString(36).substr(2, 9),
                                    option_text: o.option_text
                                }))
                            };
                        });
                    } else {
                        this.addQuestion();
                    }
                },

                addQuestion() {
                    this.questions.push({
                        id: null,
                        temp_id: Math.random().toString(36).substr(2, 9),
                        question_text: '',
                        correct_index: 0,
                        options: [
                            { temp_id: Math.random().toString(36).substr(2, 9), option_text: '' },
                            { temp_id: Math.random().toString(36).substr(2, 9), option_text: '' }
                        ]
                    });
                },

                removeQuestion(index) {
                    if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
                        this.questions.splice(index, 1);
                    }
                },

                addOption(qIndex) {
                    this.questions[qIndex].options.push({
                        temp_id: Math.random().toString(36).substr(2, 9),
                        option_text: ''
                    });
                },

                removeOption(qIndex, oIndex) {
                    if (this.questions[qIndex].options.length <= 2) {
                        alert('Minimal harus ada 2 pilihan jawaban.');
                        return;
                    }
                    this.questions[qIndex].options.splice(oIndex, 1);
                    
                    // Adjust correct index if needed
                    if (this.questions[qIndex].correct_index >= this.questions[qIndex].options.length) {
                        this.questions[qIndex].correct_index = this.questions[qIndex].options.length - 1;
                    }
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
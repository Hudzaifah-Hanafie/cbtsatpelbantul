<x-admin-layout>
    <x-slot name="header">
        {{ __('Kelola Soal: ' . $test->title) }}
    </x-slot>

    <!-- 1. Simpan data ke Global Variable -->
    <script>
        window.initialQuestionsData = @json($test->questions);
    </script>

    <div class="py-12" x-data="bulkEditor(window.initialQuestionsData)">
        <div class="max-w-7xl mx-auto">
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-sm">
                    <strong>Ada kesalahan input:</strong>
                    <ul class="list-disc ml-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Section Token -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Token Akses Ujian</h3>
                        <p class="text-sm text-gray-500">Token ini digunakan peserta untuk memulai ujian.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 w-full md:w-auto">
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

            <!-- Form Utama -->
            <form action="{{ route('admin.tests.update_bulk', $test->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section Metadata Test -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Dasar Ujian</h3>
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
                <div class="space-y-8">
                    <template x-for="(question, qIndex) in questions" :key="question.temp_id">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-xl p-6 border-l-8 border-indigo-600 relative border border-gray-200">
                            
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-extrabold text-indigo-900" x-text="'PERTANYAAN #' + (qIndex + 1)"></h3>
                                <button type="button" @click="removeQuestion(qIndex)" class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-bold text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Soal
                                </button>
                            </div>

                            <input type="hidden" x-bind:name="'questions[' + qIndex + '][id]'" x-model="question.id">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Teks Pertanyaan</label>
                                    <textarea 
                                        x-bind:name="'questions[' + qIndex + '][question_text]'" 
                                        x-model="question.question_text"
                                        rows="4" 
                                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 border-gray-300"
                                        placeholder="Tulis pertanyaan di sini..." required></textarea>
                                </div>
                                
                                <div class="md:col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Soal (Optional)</label>
                                    <div class="mt-1 flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition relative group"
                                         :class="question.error ? 'border-red-400 bg-red-50' : ''">
                                        
                                        <template x-if="question.image_url">
                                            <div class="relative w-full text-center">
                                                <img :src="question.image_url" class="max-h-40 mx-auto rounded shadow-sm border border-gray-200 mb-2">
                                                <button type="button" @click="removeImage(qIndex)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>

                                        <label x-show="!question.image_url" :for="'q-file-' + qIndex" class="text-center cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="text-xs text-gray-500 mt-2 font-bold">Klik untuk upload gambar</p>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter font-bold">JPG, PNG, WebP | Max: 5MB</p>
                                        </label>

                                        <input type="file" :id="'q-file-' + qIndex" class="hidden" accept="image/*" 
                                               x-bind:name="'questions[' + qIndex + '][image]'"
                                               @change="fileChosen($event, qIndex)">
                                        
                                        <input type="hidden" x-bind:name="'questions[' + qIndex + '][remove_image]'" x-model="question.remove_image">
                                    </div>
                                    <template x-if="question.error">
                                        <p class="text-red-600 text-[11px] mt-1 font-bold italic" x-text="question.error"></p>
                                    </template>
                                </div>
                            </div>

                            <!-- Opsi Jawaban -->
                            <div class="ml-4 pl-6 border-l-4 border-gray-100">
                                <label class="block text-indigo-900 text-sm font-extrabold mb-4 uppercase tracking-wider">Pilihan Jawaban</label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="(option, oIndex) in question.options" :key="option.temp_id">
                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 relative group">
                                            <div class="flex items-start gap-3">
                                                <div class="pt-2">
                                                    <input type="radio" 
                                                        x-bind:name="'questions[' + qIndex + '][correct_index]'" 
                                                        :value="oIndex" 
                                                        x-model="question.correct_index"
                                                        class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 cursor-pointer" required>
                                                </div>
                                                
                                                <div class="flex-1 space-y-3">
                                                    <x-input type="text" 
                                                        x-bind:name="'questions[' + qIndex + '][options][' + oIndex + '][option_text]'" 
                                                        x-model="option.option_text"
                                                        class="w-full text-sm" 
                                                        placeholder="Tulis jawaban..." required />

                                                    <template x-if="option.image_url">
                                                        <div class="relative inline-block mt-2">
                                                            <img :src="option.image_url" class="h-20 rounded border shadow-sm">
                                                            <button type="button" @click="removeOptionImage(qIndex, oIndex)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-0.5 shadow hover:bg-red-700">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>

                                                    <div class="flex flex-col gap-1">
                                                        <label :for="'o-file-' + qIndex + '-' + oIndex" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center cursor-pointer">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            Upload Gambar
                                                        </label>
                                                        <p class="text-[9px] text-gray-400 font-bold uppercase">Max: 5MB</p>
                                                        
                                                        <input type="file" :id="'o-file-' + qIndex + '-' + oIndex" class="hidden" accept="image/*" 
                                                               x-bind:name="'questions[' + qIndex + '][options][' + oIndex + '][image]'"
                                                               @change="optionFileChosen($event, qIndex, oIndex)">
                                                        
                                                        <input type="hidden" x-bind:name="'questions[' + qIndex + '][options][' + oIndex + '][remove_image]'" x-model="option.remove_image">
                                                        <input type="hidden" x-bind:name="'questions[' + qIndex + '][options][' + oIndex + '][image_path_existing]'" x-model="option.image_path">
                                                        
                                                        <template x-if="option.error">
                                                            <p class="text-red-600 text-[10px] mt-1 font-bold italic" x-text="option.error"></p>
                                                        </template>
                                                    </div>
                                                </div>

                                                <button type="button" @click="removeOption(qIndex, oIndex)" class="text-gray-400 hover:text-red-500 pt-2 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <button type="button" @click="addOption(qIndex)" class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-4 text-gray-500 hover:text-indigo-600 hover:border-indigo-400 transition-all group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span class="font-bold">Tambah Pilihan</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                <div class="mt-10 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-lg border border-gray-200 gap-4">
                    <button type="button" @click="addQuestion()" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 px-8 rounded-xl shadow-md flex items-center justify-center transition-all transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        TAMBAH PERTANYAAN BARU
                    </button>

                    <div class="flex gap-4 w-full md:w-auto">
                        <a href="{{ route('admin.tests.index') }}" class="flex-1 md:flex-none text-center text-gray-600 hover:text-gray-800 font-bold py-3 px-6 rounded-xl border border-gray-300 hover:bg-gray-50 transition">
                            KEMBALI
                        </a>
                        <button type="submit" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-extrabold py-3 px-10 rounded-xl shadow-md transition-all transform active:scale-95">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function bulkEditor(initialData) {
            return {
                questions: [],
                MAX_SIZE: 5 * 1024 * 1024, // 5MB

                init() {
                    if (initialData && initialData.length > 0) {
                        this.questions = initialData.map(q => {
                            let correctIndex = q.options.findIndex(o => o.is_correct == 1);
                            if (correctIndex === -1) correctIndex = 0;

                            return {
                                id: q.id,
                                temp_id: Math.random().toString(36).substr(2, 9),
                                question_text: q.question_text,
                                image_path: q.image_path,
                                image_url: q.image_path ? `/storage/${q.image_path}` : null,
                                remove_image: 0,
                                error: null,
                                correct_index: correctIndex,
                                options: q.options.map(o => ({
                                    id: o.id,
                                    temp_id: Math.random().toString(36).substr(2, 9),
                                    option_text: o.option_text,
                                    image_path: o.image_path,
                                    image_url: o.image_path ? `/storage/${o.image_path}` : null,
                                    remove_image: 0,
                                    error: null
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
                        image_url: null,
                        remove_image: 0,
                        error: null,
                        correct_index: 0,
                        options: [
                            { temp_id: Math.random().toString(36).substr(2, 9), option_text: '', image_url: null, remove_image: 0, error: null },
                            { temp_id: Math.random().toString(36).substr(2, 9), option_text: '', image_url: null, remove_image: 0, error: null }
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
                        option_text: '',
                        image_url: null,
                        remove_image: 0,
                        error: null
                    });
                },

                removeOption(qIndex, oIndex) {
                    if (this.questions[qIndex].options.length <= 2) {
                        alert('Minimal harus ada 2 pilihan jawaban.');
                        return;
                    }
                    this.questions[qIndex].options.splice(oIndex, 1);
                    if (this.questions[qIndex].correct_index >= this.questions[qIndex].options.length) {
                        this.questions[qIndex].correct_index = this.questions[qIndex].options.length - 1;
                    }
                },

                fileChosen(event, qIndex) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validasi Client-side
                    if (file.size > this.MAX_SIZE) {
                        this.questions[qIndex].error = 'The image must not be greater than 5 mb';
                        this.questions[qIndex].image_url = null;
                        event.target.value = '';
                        return;
                    }

                    this.questions[qIndex].error = null;
                    this.questions[qIndex].image_url = URL.createObjectURL(file);
                    this.questions[qIndex].remove_image = 0;
                },

                removeImage(qIndex) {
                    this.questions[qIndex].image_url = null;
                    this.questions[qIndex].remove_image = 1;
                    this.questions[qIndex].error = null;
                    document.getElementById('q-file-' + qIndex).value = '';
                },

                optionFileChosen(event, qIndex, oIndex) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validasi Client-side
                    if (file.size > this.MAX_SIZE) {
                        this.questions[qIndex].options[oIndex].error = 'The image must not be greater than 5 mb';
                        this.questions[qIndex].options[oIndex].image_url = null;
                        event.target.value = '';
                        return;
                    }

                    this.questions[qIndex].options[oIndex].error = null;
                    this.questions[qIndex].options[oIndex].image_url = URL.createObjectURL(file);
                    this.questions[qIndex].options[oIndex].remove_image = 0;
                },

                removeOptionImage(qIndex, oIndex) {
                    this.questions[qIndex].options[oIndex].image_url = null;
                    this.questions[qIndex].options[oIndex].remove_image = 1;
                    this.questions[qIndex].options[oIndex].error = null;
                    document.getElementById('o-file-' + qIndex + '-' + oIndex).value = '';
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>

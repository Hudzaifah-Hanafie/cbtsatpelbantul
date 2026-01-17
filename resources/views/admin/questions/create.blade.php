<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Soal untuk: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('admin.questions.store', $test->id) }}" method="POST">
                        @csrf
                        
                        <!-- Pertanyaan -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="question_text">
                                Pertanyaan
                            </label>
                            <textarea name="question_text" id="question_text" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('question_text') }}</textarea>
                            @error('question_text') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Opsi Jawaban -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Pilihan Jawaban
                            </label>
                            <div class="space-y-3">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex items-center">
                                        <input type="radio" name="correct_option_index" value="{{ $i }}" class="mr-2" required {{ old('correct_option_index') == $i ? 'checked' : '' }}>
                                        <input type="text" name="options[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Pilihan {{ $i+1 }}" required value="{{ old('options.'.$i) }}">
                                    </div>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-500 mt-1">* Pilih radio button di sebelah kiri untuk menandai jawaban yang benar.</p>
                            @error('options') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan Soal
                            </button>
                            <a href="{{ route('admin.tests.index') }}" class="text-gray-500 hover:text-gray-700">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

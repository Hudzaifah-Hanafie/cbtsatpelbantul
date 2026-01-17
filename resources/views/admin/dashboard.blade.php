<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Selamat datang, Admin!
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.tests.index') }}" class="block p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 border border-indigo-200">
                            <h3 class="font-bold text-indigo-700">Manajemen Soal</h3>
                            <p class="text-sm text-gray-600">Tambah, Edit, Hapus soal ujian.</p>
                        </a>
                        <a href="{{ route('admin.results.index') }}" class="block p-6 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200">
                            <h3 class="font-bold text-green-700">Hasil Ujian</h3>
                            <p class="text-sm text-gray-600">Lihat nilai seluruh peserta.</p>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="block p-6 bg-yellow-50 rounded-lg hover:bg-yellow-100 border border-yellow-200">
                            <h3 class="font-bold text-yellow-700">Manajemen User</h3>
                            <p class="text-sm text-gray-600">Kelola data pengguna.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

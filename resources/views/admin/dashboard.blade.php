<x-admin-layout>
    <x-slot name="header">
        {{ __('Admin Dashboard') }}
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Selamat datang, Admin!</h2>
                <p class="text-gray-600 mb-6">Kelola seluruh kegiatan ujian dari panel ini.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('admin.tests.index') }}" class="block p-6 bg-indigo-50 rounded-xl hover:bg-indigo-100 border border-indigo-200 transition shadow-sm hover:shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-indigo-600 rounded-full text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-indigo-700 text-lg">Manajemen Soal</h3>
                        </div>
                        <p class="text-sm text-gray-600">Buat ujian baru, edit soal, dan atur token akses.</p>
                    </a>

                    <a href="{{ route('admin.results.index') }}" class="block p-6 bg-green-50 rounded-xl hover:bg-green-100 border border-green-200 transition shadow-sm hover:shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-600 rounded-full text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-green-700 text-lg">Hasil Ujian</h3>
                        </div>
                        <p class="text-sm text-gray-600">Pantau nilai peserta dan export data ke Excel.</p>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="block p-6 bg-yellow-50 rounded-xl hover:bg-yellow-100 border border-yellow-200 transition shadow-sm hover:shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-yellow-600 rounded-full text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-yellow-700 text-lg">Manajemen User</h3>
                        </div>
                        <p class="text-sm text-gray-600">Kelola data peserta dan akun admin.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Peserta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Informasi Akun</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap</p>
                                <p class="text-lg text-gray-800">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Alamat Email</p>
                                <p class="text-lg text-gray-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Role</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 text-center md:text-left">Menu Utama</h3>
                        <div class="flex justify-center md:justify-start">
                            <a href="{{ route('tests.index') }}" class="w-full md:w-auto flex items-center justify-center px-8 py-4 bg-indigo-600 border border-transparent rounded-xl font-bold text-lg text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150 shadow-lg group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Lihat Daftar Ujian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
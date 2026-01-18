<x-admin-layout>
    <x-slot name="header">
        {{ __('Hasil Ujian Peserta') }}
    </x-slot>

    @php
        // Helper untuk link sort (dibungkus agar tidak error redeclare)
        if (!function_exists('sort_link')) {
            function sort_link($column, $title) {
                $currentSort = request('sort', 'completed_at');
                $currentDir = request('direction', 'desc');
                
                $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
                
                $icon = '';
                if ($currentSort === $column) {
                    $icon = $currentDir === 'asc' ? ' ▴' : ' ▾';
                }
                
                $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDir, 'page' => 1]);
                
                return '<a href="'.$url.'" class="hover:text-indigo-600 flex items-center">'.$title.'<span class="ml-1 text-indigo-500 font-bold">'.$icon.'</span></a>';
            }
        }
    @endphp

    <div class="max-w-7xl mx-auto" x-data="{ showBulkModal: false, verificationText: '' }">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filter & Export Section -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <form method="GET" action="{{ route('admin.results.index') }}" class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Filter Test -->
                            <div>
                                <label for="test_id" class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter Ujian</label>
                                <select name="test_id" id="test_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Semua Ujian --</option>
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                            {{ $test->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Peserta</label>
                                <x-input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama/Email..." class="text-sm w-full" />
                            </div>

                            <!-- Date Filters -->
                            <div>
                                <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Mulai</label>
                                <x-input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="text-sm w-full" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Selesai</label>
                                <x-input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="text-sm w-full" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-2 mt-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'test_id', 'start_date', 'end_date', 'sort']))
                                <a href="{{ route('admin.results.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                    Reset
                                </a>
                                
                                <!-- Tombol Bulk Delete (Hanya muncul jika ada filter) -->
                                <button type="button" @click="showBulkModal = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm ml-auto">
                                    Bulk Delete
                                </button>
                            @endif
                            
                            <a href="{{ route('admin.results.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm @if(!request()->hasAny(['search', 'test_id', 'start_date', 'end_date', 'sort'])) ml-auto @endif">
                                Export
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {!! sort_link('user_name', 'Nama Peserta') !!}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {!! sort_link('test_title', 'Ujian') !!}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {!! sort_link('score', 'Skor') !!}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {!! sort_link('completed_at', 'Waktu Selesai') !!}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($results as $result)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $result->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $result->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $result->test->title }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-700">{{ $result->score }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $result->completed_at ? $result->completed_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form action="{{ route('admin.results.destroy', $result->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" onclick="return confirm('Apakah Anda yakin ingin menghapus data hasil ujian ini? Data jawaban terkait juga akan dihapus.')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Tidak ada data yang ditemukan.</td>
                                </tr>
                            @endforelse
                            @if(count($results) > 0)
                                <tr class="bg-gray-50">
                                    <td colspan="5" class="px-6 py-2 text-right text-xs text-gray-500">
                                        Menampilkan {{ $results->firstItem() }} - {{ $results->lastItem() }} dari {{ $results->total() }} data
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Kanan Bawah -->
                <div class="mt-4 flex justify-end">
                    {{ $results->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Bulk Delete -->
    <div x-show="showBulkModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showBulkModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showBulkModal" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Konfirmasi Hapus Masal</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda akan menghapus data hasil ujian secara permanen berdasarkan filter yang sedang aktif. 
                                        Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                    <div class="mt-4 bg-gray-50 p-3 rounded text-xs text-gray-600">
                                        <strong>Filter Aktif:</strong><br>
                                        Ujian ID: {{ request('test_id') ?: '-' }}<br>
                                        Search: {{ request('search') ?: '-' }}<br>
                                        Tanggal: {{ request('start_date') }} s/d {{ request('end_date') }}
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ketik kalimat berikut untuk konfirmasi:</label>
                                        <p class="text-sm font-bold text-red-600 select-all mb-2">hapus semua data hasil ujian tersebut</p>
                                        <input type="text" x-model="verificationText" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Ketik di sini...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <form action="{{ route('admin.results.bulk_destroy', request()->query()) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    :disabled="verificationText !== 'hapus semua data hasil ujian tersebut'"
                                    :class="verificationText === 'hapus semua data hasil ujian tersebut' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-gray-300 cursor-not-allowed'"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Konfirmasi Hapus
                            </button>
                        </form>
                        <button type="button" @click="showBulkModal = false; verificationText = ''" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
## 1. Database & Model Setup

- [x] 1.1 Buat migration untuk menambah kolom `image_path` (nullable string) di tabel `questions` dan `options`.
- [x] 1.2 Jalankan perintah `php artisan migrate`.
- [x] 1.3 Update `$fillable` property pada model `Question` dan `Option` untuk menyertakan `image_path`.
- [x] 1.4 Jalankan `php artisan storage:link` untuk memastikan folder public storage dapat diakses.

## 2. Admin Bulk Editor (Backend)

- [x] 2.1 Modifikasi `AdminController@storeBulk` untuk menangani upload file gambar dari request array.
- [x] 2.2 Implementasi validasi file (5MB, format: jpg,jpeg,png,webp) di dalam controller.
- [x] 2.3 Tambahkan logika penghapusan file lama di storage ketika gambar diganti atau dihapus.
- [x] 2.4 Tambahkan logika penghapusan file fisik saat soal/opsi dihapus permanen.

## 3. Admin Bulk Editor (Frontend)

- [x] 3.1 Update `manage.blade.php` untuk menambahkan `enctype="multipart/form-data"` pada form utama.
- [x] 3.2 Tambahkan input file (`<input type="file">`) pada template soal di loop Alpine.js.
- [x] 3.3 Tambahkan input file pada template opsi jawaban di loop Alpine.js.
- [x] 3.4 Implementasi preview gambar instan menggunakan Alpine.js (`FileReader` API) untuk soal dan opsi.
- [x] 3.5 Tambahkan tombol "Hapus Gambar" untuk mengosongkan pilihan gambar sebelum simpan.

## 4. Exam Taking UI

- [x] 4.1 Update `tests/show.blade.php` untuk merender gambar soal (jika `image_path` ada) di atas teks pertanyaan.
- [x] 4.2 Update `tests/show.blade.php` untuk merender gambar di dalam label setiap pilihan jawaban jika tersedia.
- [x] 4.3 Pastikan styling CSS untuk gambar bersifat responsive (e.g., `max-w-full h-auto rounded`).

## 5. Verification

- [x] 5.1 Tes upload gambar pada soal dan simpan.
- [x] 5.2 Tes upload gambar pada opsi jawaban dan simpan.
- [x] 5.3 Verifikasi gambar muncul dengan benar di halaman pengerjaan ujian peserta.
- [x] 5.4 Tes penghapusan gambar dan pastikan file terhapus dari storage.

## 6. Perbaikan Bug Selama Verifikasi

- [x] 6.1 Perbaiki error `Undefined variable $qIndex` dengan mengganti template literal JS `${qIndex}` menjadi konkatenasi string di file Blade.
- [x] 6.2 Perbaiki error `Undefined constant "qIndex"` dengan mengubah binding komponen Blade dari `:name` menjadi `x-bind:name` untuk evaluasi AlpineJS.
- [x] 6.3 Perbaiki masalah posisi upload gambar (selalu ke soal terakhir) dengan mengganti pemicu `x-ref` dengan ID unik dan interaksi `<label>` native.
- [x] 6.4 Implementasi validasi real-time sisi klien untuk ukuran file (maks 5MB) untuk mencegah kegagalan validasi server dan kehilangan data.
- [x] 6.5 Tambahkan teks informatif tentang format dan ukuran maksimal yang diizinkan di antarmuka unggahan.

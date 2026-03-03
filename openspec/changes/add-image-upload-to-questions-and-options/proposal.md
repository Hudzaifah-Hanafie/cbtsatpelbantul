## Why

Saat ini sistem CBT hanya mendukung teks untuk soal dan pilihan jawaban. Banyak tipe ujian memerlukan bantuan visual (seperti diagram, grafik, atau gambar) agar pertanyaan menjadi jelas. Selain itu, fitur ini memungkinkan pembuatan soal di mana peserta harus memilih gambar yang tepat sebagai jawaban.

## What Changes

- **Pembaruan Database**: Menambahkan kolom `image_path` pada tabel `questions` dan `options`.
- **Bulk Editor Dinamis**: Menambahkan input file pada setiap baris soal dan opsi di halaman Admin Manage Test.
- **Validasi Upload**: Implementasi validasi file (Maksimal 5MB, format: jpg, jpeg, png, webp).
- **Sistem Penyimpanan**: Menggunakan disk `public` Laravel untuk menyimpan file gambar.
- **Tampilan Ujian Peserta**: Memperbarui halaman pengerjaan soal agar merender gambar di atas teks pertanyaan atau di dalam label pilihan jawaban jika gambar tersedia.
- **Fitur Hapus Gambar**: Kemampuan untuk menghapus atau mengganti gambar yang sudah diupload.

## Capabilities

### New Capabilities
- `image-management`: Penanganan backend untuk proses upload, validasi file, dan penghapusan file dari storage.

### Modified Capabilities
- `exam-management`: Pembaruan UI Bulk Editor (Alpine.js) untuk mendukung input file dan preview gambar secara dinamis.
- `exam-taking`: Pembaruan tampilan soal (Blade) agar responsif dalam menampilkan gambar baik di area pertanyaan maupun pilihan jawaban.

## Impact

- **Models**: `Question` dan `Option` (penambahan fillable `image_path`).
- **Controllers**: `AdminController` (logika penyimpanan file di `storeBulk`).
- **Views**: `admin.tests.manage` (UI input file) dan `tests.show` (UI render gambar).
- **Storage**: Penggunaan folder `storage/app/public/images` dan kebutuhan menjalankan `php artisan storage:link`.

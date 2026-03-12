## Context

Sistem saat ini menyimpan data soal dan opsi hanya dalam bentuk teks string. Untuk mendukung gambar, kita perlu menambahkan referensi lokasi file pada database dan mekanisme untuk mengunggah serta menyajikan file tersebut melalui disk publik Laravel.

## Goals / Non-Goals

**Goals:**
- Menambahkan kolom `image_path` pada tabel `questions` dan `options`.
- Mengimplementasikan logika upload file yang aman (validasi tipe dan ukuran).
- Memperbarui UI Bulk Editor agar Admin dapat mengunggah, melihat preview, dan menghapus gambar.
- Memastikan gambar muncul di halaman pengerjaan ujian peserta secara responsif.

**Non-Goals:**
- Fitur editing gambar (crop/resize) di sisi client.
- Support untuk file video atau audio (fokus hanya pada gambar).
- Integrasi Cloud Storage eksternal (menggunakan local disk `public` terlebih dahulu).

## Decisions

1. **Storage Strategy**: Menggunakan disk `public` Laravel (`storage/app/public/images`).
   - *Rationale*: Mudah dikelola dan standard untuk aplikasi Laravel. Membutuhkan `php artisan storage:link`.
2. **Database Schema**: Menambahkan kolom `image_path` (nullable string) pada tabel `questions` dan `options`.
   - *Rationale*: Nullable karena tidak semua soal/opsi memiliki gambar.
3. **Bulk Upload Handling**: Form di Bulk Editor akan diubah menjadi `multipart/form-data`.
   - *Rationale*: Diperlukan untuk mengirim file biner melalui POST/PUT request.
4. **Alpine.js for Preview**: Menggunakan Alpine.js untuk menangani preview gambar instan saat file dipilih tanpa perlu submit form terlebih dahulu.
   - *Rationale*: Meningkatkan UX Admin saat mengelola bank soal.

## Risks / Trade-offs

- **[Risk]**: Ukuran storage membengkak karena gambar berkualitas tinggi.
  - **Mitigation**: Membatasi ukuran upload maksimal 5MB dan memberikan peringatan pada UI.
- **[Risk]**: Orphan files (file yang tersimpan di storage tapi datanya sudah dihapus dari DB).
  - **Mitigation**: Menambahkan logika penghapusan file di Service saat data soal/opsi dihapus atau gambarnya diganti.
- **[Risk]**: Concurrency saat bulk update dengan file banyak.
  - **Mitigation**: Menggunakan `DB::transaction` untuk memastikan konsistensi data DB dengan state file (meskipun rollback file lebih sulit, integritas DB tetap terjaga).

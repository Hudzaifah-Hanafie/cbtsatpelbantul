## ADDED Requirements

### Requirement: Image upload and storage
Sistem HARUS mampu menerima, memvalidasi, dan menyimpan file gambar yang diunggah oleh admin untuk entitas soal dan opsi.

#### Scenario: Successful image upload
- **WHEN** Admin mengunggah file gambar (jpg/jpeg/png/webp) dengan ukuran < 5MB
- **THEN** Sistem menyimpan file ke storage publik dan mencatat path-nya di database

#### Scenario: Failed upload due to size
- **WHEN** Admin mengunggah file gambar dengan ukuran > 5MB
- **THEN** Sistem menolak file tersebut dan menampilkan pesan error validasi

#### Scenario: Failed upload due to format
- **WHEN** Admin mengunggah file non-gambar (misal: pdf atau exe)
- **THEN** Sistem menolak file tersebut dan menampilkan pesan error format tidak didukung

### Requirement: Image removal
Sistem HARUS mampu menghapus file fisik dari storage ketika Admin memilih untuk menghapus gambar dari soal/opsi atau ketika soal/opsi tersebut dihapus secara permanen.

#### Scenario: Delete image reference
- **WHEN** Admin menekan tombol hapus gambar pada baris soal di Bulk Editor
- **THEN** Sistem menghapus file dari storage dan mengosongkan kolom image_path di database

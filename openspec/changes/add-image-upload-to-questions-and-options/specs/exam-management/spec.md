## ADDED Requirements

### Requirement: Bulk Editor image support
Antarmuka Bulk Editor HARUS menyediakan input file untuk setiap butir soal dan setiap pilihan jawaban.

#### Scenario: Display image preview
- **WHEN** Admin memilih file gambar pada input file di baris soal
- **THEN** Antarmuka menampilkan preview gambar secara instan (menggunakan Alpine.js) sebelum form disimpan

#### Scenario: Existing image display
- **WHEN** Admin membuka halaman Manage Test untuk ujian yang soalnya sudah memiliki gambar
- **THEN** Antarmuka menampilkan gambar yang sudah ada pada baris soal/opsi terkait

## ADDED Requirements

### Requirement: Responsive image rendering in test
Halaman pengerjaan ujian HARUS merender gambar soal dan gambar opsi jawaban secara proporsional.

#### Scenario: Question with image
- **WHEN** Peserta mengakses halaman soal yang memiliki gambar
- **THEN** Gambar ditampilkan di atas teks pertanyaan dengan lebar maksimal sesuai kontainer (responsive)

#### Scenario: Option with image
- **WHEN** Sebuah pilihan jawaban (opsi) memiliki gambar
- **THEN** Gambar ditampilkan di dalam label pilihan jawaban di atas atau di samping teks opsi tersebut

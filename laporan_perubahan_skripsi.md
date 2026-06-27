# Rangkuman Refaktor Database & Sistem Skripsi E-Absensi

Dokumen ini merupakan panduan penjelasan teknis yang telah diterapkan pada aplikasi absensi. Rangkuman ini sangat relevan untuk dipresentasikan atau dijelaskan kepada Dosen Pembimbing sebagai argumen yang kuat secara akademis (khususnya untuk Bab III dan IV terkait perancangan *Database*).

---

> [!TIP]
> **Tujuan Utama Refaktor**
> Menyelaraskan implementasi *database* fisik (*Physical Data Model*) agar 100% konsisten dengan desain konseptual yang tertulis di skripsi (ERD / *Logical Record Structure*), serta meningkatkan performa memori.

## 1. Transisi dari *Surrogate Keys* ke *Natural Keys*
Sebelumnya, sistem Laravel secara bawaan membuat ID buatan otomatis berupa angka berurut (`id` *auto-increment*) pada semua tabel. Meskipun ini praktis, metode tersebut kurang sesuai dengan pendekatan akademis *database* yang baik. Kami telah merombaknya dengan menggunakan atribut identitas asli (**Natural Keys**) sebagai *Primary Key*.

**Perubahan Konkret di Tabel Utama:**
*   **Tabel `students` (Siswa):** *Primary Key* bukan lagi `id`, melainkan diubah menjadi atribut unik bawaan yaitu **`nisn`** (Nomor Induk Siswa Nasional).
*   **Tabel `homeroom_teachers` (Wali Kelas):** *Primary Key* menggunakan **`nip`** (Nomor Induk Pegawai).
*   **Tabel `parents` (Orang Tua):** *Primary Key* menggunakan **`nik`** (Nomor Induk Kependudukan).
*   **Tabel `classes` (Kelas) & `subjects` (Mata Pelajaran):** *Primary Key* menggunakan **`code`** (Kode unik kelas/mapel seperti `X-RPL-1` atau `MTK`).
*   **Tabel `users` (Pengguna Akun):** *Primary Key* diubah dari `id` menjadi **`username`**.

**Argumen untuk Dosen:**
> *"Pemilihan Natural Keys menghilangkan kolom-kolom buatan (dummy) yang redundan. Hal ini membuktikan bahwa ERD yang dirancang di Bab 3 langsung bisa diimplementasikan persis (mapping 1:1) tanpa perlu kolom tambahan yang tidak punya arti di dunia nyata."*

## 2. Optimasi Alokasi Tipe Data Karakter (*Varchar Limiting*)
Laravel secara default memberikan ukuran karakter maksimal, yakni `VARCHAR(255)`, pada setiap kolom *string*. Kami telah memangkasnya secara ketat berdasarkan logika bisnis *(business rules)* guna meminimalisir beban *storage* dan mempercepat *indexing*.

**Contoh Restriksi yang Diterapkan:**
*   `email` -> `VARCHAR(100)` (Karena tidak ada format email hingga 255 karakter).
*   `name` -> `VARCHAR(100)` (Cukup untuk menampung seluruh kemungkinan nama).
*   `phone_number` -> `VARCHAR(20)`.
*   `nisn`, `nip`, `code` -> `VARCHAR(20)`.
*   `nik` -> `VARCHAR(16)` (Baku KTP Indonesia).
*   `username` -> `VARCHAR(50)`.

**Argumen untuk Dosen:**
> *"Pembatasan ukuran VARCHAR menunjukkan bahwa perancangan spesifikasi tabel benar-benar mempertimbangkan limitasi memori fisik server. Hal ini mencegah alokasi penyimpanan kosong yang mubazir pada basis data berkapasitas besar dan merupakan standar (Best Practice) dalam rancang bangun perangkat lunak profesional."*

## 3. Keselarasan Relasi Data (Foreign Keys)
Karena *Primary Key* berubah dari angka ke *string* / teks, seluruh relasi antar-tabel ikut diperbarui secara global.
*   **Contoh Tabel `absences` (Absensi):** Tidak lagi menampung `student_id = 5`, melainkan menampung `student_nisn = 0012345678`.
*   **Contoh Tabel `students` (Siswa):** Tidak lagi menggunakan `class_id = 2`, melainkan menggunakan `class_code = X-RPL-1`.

**Argumen untuk Dosen:**
> *"Dengan menggunakan identitas asli yang bisa dibaca manusia sebagai Foreign Key, query dan laporan database jauh lebih mudah ditelusuri. Kita tidak perlu menebak 'Siswa ID 5 itu siapa?', karena 'NISN 0012345678' sudah berbicara sendiri tanpa harus men-join tabel berkali-kali."*

## 4. Penyesuaian Sisi Kode (*Source Code*)
Untuk mendukung perubahan mendasar tersebut, seluruh lapisan kode (*MVC Pattern*) disesuaikan:
*   **Models:** Seluruh model *(Student, User, ClassModel, dll)* ditambahkan deklarasi:
    ```php
    protected $primaryKey = 'nisn'; 
    public $incrementing = false; 
    protected $keyType = 'string';
    ```
    (Memberi instruksi ke Framework bahwa *key* kita bukan angka berurutan, melainkan teks unik).
*   **Controllers:** Fungsi logika program di seluruh bagian (Admin dan Wali Kelas) dikalibrasi untuk menangkap parameter URL berupa identitas teks, bukan ID statis.
*   **Views (UI):** Ratusan pemanggilan variabel dalam antarmuka HTML (seperti `$student->id`) diganti dengan pemanggilan akurat (seperti `$student->nisn`).

## 5. Pembersihan & Stabilitas Sistem
*   **Resolusi Folder Virtual (`symlink`):** Mengatasi eror penyimpanan lokal akibat konflik *shortcut* memori (folder `public/storage`), sehingga kini proses unggah Logo Kop Sekolah / Surat Keterangan berjalan sempurna.
*   **Penghapusan File Ganda:** Menghapus salinan *folder root* berukuran ratusan Megabyte yang terselip (`skripsi-absensi/skripsi-absensi`) untuk merampingkan aset ketika di-*deploy*.
*   **Pembaruan Repositori:** Seluruh kode final yang rapi telah sinkron dengan repositori *online* (GitHub), memfasilitasi kebutuhan verifikasi *source code* jarak jauh secara aman.

---
*Laporan ini dirangkum secara khusus sebagai dokumen pendukung sidang atau proses bimbingan.*

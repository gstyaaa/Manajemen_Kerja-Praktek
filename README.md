# KP-Infor - Kerja Praktik Informatika

Sistem Informasi Pengajuan dan Monitoring Kerja Praktik Mahasiswa Informatika.

Aplikasi berbasis web untuk mempermudah administrasi Kerja Praktik (KP) di lingkungan kampus antara **Mahasiswa**, **Dosen Pembimbing**, dan **Admin/Prodi**. Aplikasi ini dirancang dari awal menggunakan **Laravel 13**, **Livewire 4**, dan **Tailwind CSS**.

---

## 🚀 Fitur Utama & Logika Sistem

1. **Autentikasi Multi-Role & Keamanan**:
   - Tiga role pengguna: `admin` (Prodi), `mahasiswa`, dan `dosen`.
   - Registrasi publik dinonaktifkan demi menjaga integritas role system. Akun mahasiswa dan dosen hanya dapat dibuat oleh Admin.
   - Hak akses diproteksi ketat menggunakan middleware `auth` dan custom middleware `role:admin`, `role:mahasiswa`, `role:dosen`.

2. **Manajemen Kuota Dosen Pembimbing (Default Kuota: 5)**:
   - Dihitung secara otomatis di backend: `sisa slot = kuota bimbingan - jumlah pengajuan aktif`.
   - **Status Aktif (Memakai Slot)**: `diajukan`, `menunggu_konfirmasi_dosen`, `disetujui_dosen`, `diterima_admin`, `revisi`, `kp_berlangsung`, `laporan_diupload`.
   - **Status Inaktif (Melepas Slot)**: `draft`, `ditolak_dosen`, `ditolak_admin`, `dibatalkan`, `selesai`.
   - Validasi backend ketat: Jika sisa slot dosen $\le 0$ saat pengajuan dikirim, sistem menolak submit dengan pesan error: *"Kuota dosen pembimbing sudah penuh. Silakan pilih dosen lain."*

3. **Konfirmasi Pengajuan oleh Dosen**:
   - Dosen dapat menyetujui (`status_pengajuan` menjadi `disetujui_dosen` dan `status_persetujuan_dosen` menjadi `disetujui`) atau menolak pengajuan (`status_pengajuan` menjadi `ditolak_dosen` dan `status_persetujuan_dosen` menjadi `ditolak`) beserta catatan penolakan.
   - Menolak pengajuan akan mengembalikan slot dosen secara otomatis sehingga mahasiswa dapat memilih dosen lain.

4. **Sistem Upload Dokumen & Laporan (Livewire 4 File Upload)**:
   - Mahasiswa mengunggah 4 jenis file: Surat Pengajuan KP, Surat Penerimaan Instansi, Laporan KP, dan Lampiran.
   - Validasi ketat format file (`pdf`, `doc`, `docx`, `jpg`, `png`) dan ukuran maksimal 5MB.
   - Dilengkapi dengan *upload progress bar* dan tombol download.

5. **Monitoring Progres Realtime**:
   - Visualisasi alur proses KP berupa timeline riwayat aksi mahasiswa, dosen, dan admin.

6. **Kolom Diskusi / Komentar Bimbingan (Livewire Island Component)**:
   - Panel komentar dosen dibuat sebagai komponen terpisah yang memicu event (`commentAdded`) untuk memperbarui log komentar secara asinkron tanpa reload halaman penuh.

---

## 🛠️ Stack Teknologi
*   **Core Framework**: Laravel 13
*   **Frontend Logic**: Livewire 4
*   **Styling**: Tailwind CSS
*   **Database**: MySQL

---

## 💻 Cara Menjalankan Project

Ikuti perintah di bawah ini secara berurutan untuk menyiapkan aplikasi di komputer lokal Anda:

### 1. Kloning Project dan Masuk ke Direktori
Pastikan Anda menempatkan file project di folder server XAMPP (`htdocs`) jika ingin langsung diakses melalui domain lokal atau jalankan dengan CLI.

### 2. Instalasi Dependency
```bash
composer install
npm install
```

### 3. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`.
Untuk Windows PowerShell:
```powershell
Copy-Item .env.example .env
```
Untuk Git Bash / Command Prompt:
```bash
cp .env.example .env
```

Pastikan konfigurasi database di `.env` sudah sesuai dengan MySQL lokal Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manajemen_kp
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Membuat Database & Generate Key
Buat database bernama `manajemen_kp` di phpMyAdmin Anda terlebih dahulu, kemudian jalankan:
```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

### 5. Menjalankan Server
Jalankan server Vite dan server Laravel secara bersamaan:
```bash
npm run dev
php artisan serve
```

---

## 🔑 Akun Uji Coba (Demo Login)

Aplikasi telah dilengkapi dengan seeder akun contoh. Silakan gunakan kredensial berikut untuk masuk ke dashboard masing-masing role:

| Role | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin / Prodi** | `admin@kp.com` | `password` | Mengelola data Master, Verifikasi KP, & Akun |
| **Dosen Pembimbing 1** | `budi@kp.com` | `password` | Dr. Ir. Budi Raharjo (Sistem Informasi) |
| **Dosen Pembimbing 2** | `sri@kp.com` | `password` | Prof. Sri Lestari (Kecerdasan Buatan) |
| **Dosen Pembimbing 3** | `joko@kp.com` | `password` | Joko Susilo, M.T. (Rekayasa Perangkat Lunak) |
| **Mahasiswa 1** | `aditya@kp.com` | `password` | Aditya Pratama (IF 2023) |
| **Mahasiswa 2** | `citra@kp.com` | `password` | Citra Kirana (IF 2023) |
| **Mahasiswa 3** | `fahri@kp.com` | `password` | Fahri Hamzah (SI 2023) |

---

## 🧪 Skenario Pengujian Manual (Testing Checklist)

Berikut adalah checklist skenario pengujian manual yang berhasil diuji:

- [x] **Login Admin**: Masuk ke dashboard admin dengan statistik jumlah mahasiswa, dosen, pengajuan, dan dosen dengan slot kritis.
- [x] **CRUD Mahasiswa & Dosen oleh Admin**: Menambahkan data mahasiswa baru dan dosen pembimbing baru (sistem otomatis membuat login user terkait).
- [x] **Pemantauan Kuota Dosen**: Admin melihat sisa slot dosen pembimbing dan bimbingan aktif secara live.
- [x] **Pembuatan Pengajuan KP**: Mahasiswa membuat draf pengajuan KP baru dan memilih dosen pembimbing.
- [x] **Realtime Slot Dropdown (`wire:model.live`)**: Pilihan dosen menampilkan sisa slot dosen secara langsung. Pilihan dosen yang kuotanya penuh otomatis dinonaktifkan.
- [x] **Validasi Quota di Backend**: Menguji pengiriman pengajuan ke dosen yang kuotanya penuh akan dibatalkan di backend dan memunculkan error: *"Kuota dosen pembimbing sudah penuh. Silakan pilih dosen lain."*
- [x] **Pengurangan Slot**: Slot bimbingan dosen berkurang seketika setelah mahasiswa mengirimkan pengajuan (status `menunggu_konfirmasi_dosen`).
- [x] **Konfirmasi Setuju oleh Dosen**: Dosen membuka halaman pengajuan mahasiswa, menyetujui, dan slot bimbingan tetap terpakai. Status naik ke `disetujui_dosen`.
- [x] **Konfirmasi Tolak oleh Dosen**: Dosen menolak pengajuan mahasiswa lain disertai catatan alasan penolakan. Slot bimbingan dosen tersebut kembali dibebaskan. Mahasiswa melihat catatan penolakan di dashboard dan dapat mengajukan ulang dengan memilih dosen lain.
- [x] **Verifikasi Admin**: Admin memverifikasi pengajuan yang telah disetujui dosen, mengubah status pengajuan menjadi `kp_berlangsung`.
- [x] **Unggah Dokumen & Laporan**: Mahasiswa mengunggah Surat Penerimaan Instansi dan Laporan KP. Progres bar Livewire berjalan, file tersimpan di storage lokal. Mengunggah laporan otomatis menaikkan status menjadi `laporan_diupload`.
- [x] **Komentar Bimbingan (Island Component)**: Dosen bimbingan memberikan catatan revisi pada dokumen laporan mahasiswa. Halaman komentar pada mahasiswa terupdate seketika secara asinkron tanpa reload penuh halaman.
- [x] **Penyelesaian KP**: Dosen mengklik tombol "Selesaikan KP" yang mengubah status menjadi `selesai` dan membebaskan slot dosen tersebut kembali.

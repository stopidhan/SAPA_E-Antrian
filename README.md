# SAPA E-Antrian (Sistem Antrean Pelayanan Andal)

**SAPA E-Antrian** adalah sebuah aplikasi manajemen antrean modern, dinamis, dan terintegrasi yang dirancang untuk mempermudah alur pelayanan publik maupun swasta. Dibangun menggunakan ekosistem Laravel modern, aplikasi ini tidak hanya mencatat antrean secara konvensional, tetapi juga menghadirkan pembaruan layar secara _real-time_ dan fitur pemanggilan suara otomatis (_Text-to-Speech_) langsung dari peramban web (_browser_).

Sistem ini sangat cocok diimplementasikan pada instansi yang membutuhkan pembagian loket, pemantauan performa pelayanan, dan manajemen antrean yang tidak memberatkan _server_.

---

## ✨ Fitur Utama (Key Features)

Aplikasi ini didesain dengan mempertimbangkan berbagai studi kasus pelayanan. Berikut adalah fitur-fitur unggulannya:

- **Pembaruan Real-Time Tanpa Refresh (WebSockets):** Memanfaatkan Laravel Reverb dan Echo, setiap kali ada nomor antrean baru atau panggilan dari operator, layar pelanggan (_Customer_) akan diperbarui secara seketika tanpa perlu memuat ulang (_reload_) halaman.
- **Pemanggil Suara Otomatis (Text-to-Speech):** Tidak membutuhkan _hardware_ atau API pihak ketiga yang mahal. SAPA E-Antrian memanfaatkan **Web Speech API** bawaan _browser_ (sangat optimal pada Microsoft Edge/Chrome) untuk membacakan nomor antrean pelanggan secara jernih dan otomatis.
- **Multi-Instansi & Multi-Loket:** Sistem ini dapat menaungi lebih dari satu instansi/departemen sekaligus. Setiap instansi memiliki staf, loket, dan layanannya sendiri yang dikelola secara terpusat.
- **Role-Based Access Control (RBAC):** Keamanan hak akses yang ketat. Seorang operator hanya bisa melayani loketnya sendiri, sementara Kepala Layanan bisa melihat seluruh laporan instansi.
- **Tindakan Antrean Fleksibel:** Operator dapat melakukan pemanggilan ulang (_Recall_), melewati nomor yang tidak hadir (_Skip_), atau mengoper/transfer tiket antrean ke loket lain sesuai kebutuhan alur pelayanan.
- **Jejak Rekam Audit (Activity Logging):** Seluruh aktivitas krusial pengguna (seperti login, mengubah data, memanggil antrean) dicatat dengan rapi di latar belakang menggunakan `spatie/laravel-activitylog` untuk keperluan audit keamanan.

---

## 🛠️ Teknologi yang Digunakan (Tech Stack)

Sistem ini menerapkan standar teknologi pengembangan web masa kini:

- **Backend & Framework Core:** Laravel (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Frontend UI:** Laravel Blade, Tailwind CSS, Alpine.js
- **Real-time Engine:** Laravel Reverb (WebSocket Server) & Pusher-js / Laravel Echo (Client)
- **Audio Engine:** HTML5 Web Speech API

---

## 🚀 Panduan Instalasi (Getting Started)

Untuk para pengembang yang ingin menjalankan proyek ini secara lokal, silakan ikuti langkah-langkah detail berikut:

### 1. Persiapan Kebutuhan Lingkungan (Prerequisites)

Pastikan komputer Anda sudah terinstal perangkat lunak berikut:

- PHP >= 8.2
- Composer
- Node.js (versi LTS disarankan) & NPM
- Database Server (MySQL/MariaDB via XAMPP, Laragon, dsb.)

### 2. Langkah-Langkah Instalasi

1. **Kloning Repositori:**
    ```bash
    git clone <url-repositori-anda>
    cd sistemE-antrian
    ```
2. **Instalasi Dependensi:**
    ```bash
    composer install
    npm install
    ```
3. **Konfigurasi Lingkungan (.env):**
   Salin file konfigurasi _environment_ bawaan:

    ```bash
    cp .env.example .env
    ```

    Buka file `.env` Anda dan sesuaikan koneksi database. Pastikan juga pengaturan _Broadcast_ dan _Queue_ diatur seperti berikut untuk mendukung Reverb secara langsung:

    ```env
    DB_CONNECTION=mysql
    DB_DATABASE=nama_database_antrian_anda
    DB_USERNAME=root
    DB_PASSWORD=

    BROADCAST_CONNECTION=reverb
    ```

4. **Generate App Key:**
    ```bash
    php artisan key:generate
    ```
5. **Migrasi Database & Seeder:**
   _(Langkah ini akan membangun struktur tabel dan mengisi data instansi serta pengguna awal)_
    ```bash
    php artisan migrate --seed
    ```

### 3. Menjalankan Aplikasi

Dikarenakan ini adalah aplikasi _real-time_, Anda wajib menjalankan 3 layanan secara bersamaan di 3 jendela terminal (_command prompt_) yang berbeda:

- **Terminal 1 (PHP Server):** Menjalankan logika utama aplikasi backend.
    ```bash
    php artisan serve
    ```
- **Terminal 2 (Asset Bundler):** Mengkompilasi Tailwind CSS dan aset Javascript.
    ```bash
    npm run dev
    ```
- **Terminal 3 (WebSocket Server):** Menjalankan _port_ khusus untuk aliran data _real-time_ ke layar monitor pelanggan.
    ```bash
    php artisan reverb:start
    ```

---

## 👥 Hak Akses & Kredensial Pengguna (User Roles)

Sistem ini menyediakan hierarki pengguna yang terstruktur. Setelah Anda menjalankan proses `migrate --seed`, Anda dapat menggunakan kredensial berikut untuk melakukan pengujian (_testing_) login pada aplikasi.

_(Catatan: Angka `1` pada email di bawah merujuk pada ID Instansi. Anda dapat menggantinya dengan angka lain jika terdapat banyak instansi)._

| Role                  | Deskripsi & Hak Akses                                                                                                                        | Email Default          | Password   |
| :-------------------- | :------------------------------------------------------------------------------------------------------------------------------------------- | :--------------------- | :--------- |
| **Super Admin**       | Tingkat tertinggi. Mengelola seluruh data _master_ sistem, menambah instansi baru, mengelola _role_, dan konfigurasi global.                 | `superadmin@test.com`  | `password` |
| **Admin Instansi**    | Pemegang kendali cabang. Mengelola data jenis layanan, mendirikan loket, dan mendaftarkan pegawai (operator) pada instansinya masing-masing. | `admin1@test.com`      | `password` |
| **Kepala Layanan**    | Pihak manajerial. Memantau statistik lalu lintas pelayanan dan mencetak laporan performa antrean untuk bahan evaluasi harian/bulanan.        | `kepala1@test.com`     | `password` |
| **Staff Operator**    | Staf lapangan (Garda Depan). Menjalankan sistem pemanggilan antrean secara langsung, melakukan transfer layanan antrean, dan _skip_ nomor.   | `operator1_1@test.com` | `password` |
| **Staff Konten**      | Staf media. Bertugas mengelola konten teks berjalan (_running text_), pengumuman, atau video pada layar ruang tunggu (_Customer Display_).   | `konten1@test.com`     | `password` |
| **Customer (Publik)** | Pelanggan. Mengambil tiket antrean dan melihat pembaruan status panggilan nomor secara _real-time_ dari layar.                               | _(Tanpa Login)_        | -          |

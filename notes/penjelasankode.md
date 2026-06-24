# Rangkuman Logika & Workflow Modul SAPA E-Antrian
*(Fokus: Antarmuka Pelayanan & Pengunjung)*

Berikut adalah rangkuman dari 4 modul utama yang menjadi lingkup pengerjaan Anda. Penjelasan ini dikerucutkan pada **Alur Kerja (*Workflow*)**, **Logika Inti**, dan **Batasannya (*Constraints*)**.

---

## 🔄 Alur Global (End-to-End Workflow) & Usecase Wablas
Untuk memahami bagaimana keempat modul ini saling berkomunikasi secara berurutan, mari kita ikuti perjalanan seorang pengguna (misal: Budi) dari awal hingga akhir:

1. **Usecase Registrasi & Wablas (Modul Remote User):**
   *   Budi sedang di rumah dan ingin mengambil antrean Puskesmas/Instansi. Ia membuka aplikasi web SAPA E-Antrian.
   *   Budi menginputkan nama dan nomor WhatsApp (misal: `0812-345-678`).
   *   **Logika Integrasi Wablas:** Saat Budi menekan "Kirim OTP", sistem memicu `WablasService`. Sistem secara cerdas memformat ulang nomor HP tersebut (menghapus tanda strip dan mengubah awalan `0` menjadi `62`). Kemudian, sistem mengirim perintah `HTTP POST` berisikan token otorisasi ke *server* Wablas.
   *   Dalam hitungan detik, bot WhatsApp instansi mengirimkan pesan ke HP Budi: *"Kode OTP registrasi Anda: 5432"*. Budi memasukkan kode tersebut untuk memvalidasi nomornya (menghindari pendaftaran *spam/bot*).
   *   Setelah terotentikasi, Budi memilih layanan dan mendapatkan **Tiket QR Code digital** dengan estimasi waktu tunggu secara *real-time*.

2. **Usecase Konfirmasi Kehadiran (Modul Kiosk On-Site):**
   *   Meski sudah mendaftar *online*, nama Budi **belum** muncul di layar antrean petugas loket. Mengapa? Karena Budi belum tiba di lokasi (menghindari memanggil "orang gaib").
   *   Budi tiba di lobi instansi. Ia menuju **Mesin Kiosk** dan menodongkan tiket QR Code di HP-nya ke kamera/pemindai Kiosk.
   *   Sistem memvalidasi QR tersebut dan mengubah status Budi di *database* menjadi **"Hadir (*Checked-in*)"**. Kini, antrean Budi resmi diaktifkan dan masuk ke antrean fisik.

3. **Usecase Pemanggilan (Modul Operator Loket):**
   *   Petugas di Loket 1 melihat nomor antrean Budi muncul di daftar tunggu. Petugas menekan tombol **"Panggil"**.
   *   *(Catatan Proteksi)*: Jika petugas Loket 2 secara tidak sengaja menekan nomor Budi di milidetik yang sama persis, sistem pelindung **Atomic Lock** akan menolak perintah dari Loket 2. Budi hanya mutlak dimiliki oleh Loket 1 (mencegah tabrakan pemanggilan).

4. **Usecase Penyiaran Publik (Modul TV Monitor):**
   *   Tepat di detik yang sama saat petugas memencet tombol "Panggil", sistem menembakkan sinyal **WebSocket (Laravel Reverb)** ke TV Monitor di ruang tunggu.
   *   Tanpa jeda *refresh* halaman (*Zero-latency*), layar TV langsung berubah menampilkan nomor Budi.
   *   Fitur kecerdasan buatan dari *browser* (**Web Speech API**) membaca teks perintah dan melafalkan suara melalui *speaker* ruangan: *"Nomor Antrean A-001, silakan menuju loket satu"*.
   *   Budi maju ke meja pelayanan.

5. **Usecase Validasi Selesai (WebRTC Kamera):**
   *   Setelah urusan Budi selesai, petugas menekan tombol **"Selesai"**.
   *   Kamera (*Webcam*) pada laptop/komputer petugas otomatis mengambil jepretan foto Budi sebagai bukti digital yang tak terbantahkan bahwa antrean tersebut telah dilayani dengan baik. Alur selesai.

---

## 1. Modul Online User (Booking Online)
**Fungsi:** Mengurai kepadatan fisik di ruang tunggu dengan memungkinkan masyarakat mengambil nomor antrean dari rumah menggunakan *smartphone*.

*   **Workflow:** Pengunjung memindai poster QR/membuka link ➔ Memasukkan Nama & No. WhatsApp ➔ Menerima kode OTP via WA (*Wablas/Fonnte API*) ➔ Verifikasi OTP ➔ Memilih Layanan ➔ Menerima Tiket Digital (QR Code) berserta estimasi waktu tunggu.
*   **Logika & Batasan:**
    *   **Pencegahan Monopoli (Limitasi):** Dibatasi maksimal mengambil **2 tiket** per hari per akun. Tidak bisa mengambil tiket baru jika tiket sebelumnya belum selesai/dipanggil.
    *   **Proteksi Tabrakan Data (*Atomic Lock*):** Mengunci pangkalan data saat tiket dibuat. Jika 2 orang mendaftar di milidetik yang sama, mereka tidak akan mendapat nomor urut yang sama.
    *   **Batas Waktu Kedaluwarsa (*Expired*):** Tiket *online* bukan tiket final. Pengunjung diberi waktu **30 menit** untuk datang ke lokasi. Jika lewat batas waktu tanpa memvalidasi kehadiran, tiket otomatis **Hangus (*Skipped*)**.

## 2. Modul On-Site User (Kiosk)
**Fungsi:** Anjungan mandiri berupa *tablet/touchscreen* di lobi instansi yang bertugas sebagai resepsionis robot (*Omnichannel*).

*   **Workflow:** 
    *   *(Jalur Luring/Walk-in):* Datang ➔ Tekan tombol layanan ➔ Kiosk mencetak karcis kertas.
    *   *(Jalur Daring/Online):* Datang ➔ Buka QR Tiket di HP ➔ Sorot ke pemindai Kiosk ➔ Tiket *Online* menjadi valid.
*   **Logika & Batasan:**
    *   **Validasi Kehadiran (*Check-in*):** Kiosk adalah satu-satunya gerbang pembuka status antrean *online*. Pengguna *online* baru akan masuk ke daftar panggilan di layar Operator **hanya setelah** mereka berhasil memindai QR (Check-in) di mesin Kiosk.

## 3. Modul Operator Loket
**Fungsi:** Pusat kendali bagi petugas instansi untuk mengelola aliran pemanggilan nomor antrean tanpa perlu berteriak.

*   **Workflow:** Operator Login ➔ Masuk ke antarmuka pelayanan ➔ Menekan tombol **Panggil (Call)** untuk memanggil antrean teratas ➔ Jika pengunjung tidak datang, tekan **Lewati (Skip)** atau **Panggil Ulang (Recall)** ➔ Jika pengunjung datang dan dilayani, tekan **Selesai**.
*   **Logika & Batasan:**
    *   **Atomic Lock (Anti Rebutan):** Mencegah *Race Condition*. Jika Loket 1 dan Loket 2 menekan tombol "Panggil" untuk antrean nomor 005 secara serentak, *database* hanya akan memenangkan salah satu operator (yang tercepat walau selisih 0,001 detik). Operator yang kalah akan otomatis diblokir dari antrean tersebut.
    *   **Integrasi WebRTC (Kamera Bukti):** Saat menekan tombol "Selesai", sistem secara paksa akan mengambil jepretan dari *webcam/kamera* laptop operator. Hal ini menjadi bukti otentik bahwa pengunjung benar-benar dilayani.

## 4. Modul TV Monitor Layanan Publik
**Fungsi:** Menampilkan informasi visual nomor antrean secara raksasa dan menyiarkan pelafalan suara otomatis.

*   **Workflow:** TV menyala di ruang tunggu ➔ Diam menunggu sinyal ➔ Operator menekan tombol panggil ➔ Angka di TV berubah detik itu juga ➔ Speaker melafalkan nomor urut (contoh: "Nomor Antrean, A, Kosong, Satu, silakan ke loket dua").
*   **Logika & Batasan:**
    *   **Zero-Latency (WebSocket):** Tidak menggunakan metode *AJAX Auto-refresh* yang memberatkan server. Sistem menggunakan **Laravel Reverb (WebSockets)** untuk menyiarkan (*broadcast*) pembaruan seketika (*real-time*).
    *   **Suara Robot Internal (Web Speech API):** Menghindari penggunaan *file* audio terpotong (seperti `a.mp3`, `1.mp3`) yang memberatkan penyimpanan. Suara dihasilkan langsung oleh kecerdasan buatan (*Text-to-Speech*) bawaan *browser* TV.
    *   **Antrean Audio (*Audio Queue System*):** Jika Loket 1 dan Loket 2 memanggil bersamaan, suara robot tidak akan tabrakan/menimpa satu sama lain. Suara kedua akan "disimpan" di memori, dan dilafalkan setelah suara loket pertama selesai.

---

### 💡 Feedback & Saran Pengembangan (Dari Perspektif Developer)
1. **Fallback Mechanism pada Layar TV:** 
   Karena TV Monitor murni bergantung pada sinyal WebSocket (Reverb), sangat disarankan menambahkan logika *fallback* (cadangan). Jika sewaktu-waktu *service* Reverb mati secara mendadak, layar TV bisa otomatis beralih menggunakan sistem *AJAX Polling* (misal cek setiap 3 detik) agar pelayanan tidak lumpuh total.
2. **Alert Izin Kamera Operator:**
   Pastikan ada *pop-up blocker* yang jelas jika *browser* komputer operator secara tidak sengaja memblokir izin kamera (WebRTC). Jangan biarkan mereka memanggil antrean jika kamera tidak terdeteksi, karena akan menyebabkan proses "Selesai" gagal dieksekusi (*error* gagal mengambil bukti foto).

---

## 🎤 Simulasi Tanya Jawab (Q&A) dengan Senior Developer / Dosen Penguji
Untuk memastikan Anda benar-benar menguasai *source code* dan struktur *folder* aplikasi Anda, berikut adalah beberapa pertanyaan kritis yang biasanya dilontarkan oleh dosen penguji atau *Senior Developer*, lengkap dengan panduan cara menjawabnya secara profesional:

### Q0: Deskripsi Singkat Sistem (*Elevator Pitch*)
**Pertanyaan:** *"Coba jelaskan secara singkat dan padat, sebenarnya sistem SAPA E-Antrian yang kamu buat ini sistem yang seperti apa? Apa tujuannya, dan bagaimana alur kerjanya secara garis besar?"*
**Cara Menjawab:**
> "Baik, Pak/Bu. Secara general, SAPA E-Antrian adalah sebuah **Platform Manajemen Antrean berbasis SaaS (Software as a Service) dengan arsitektur Multi-Tenant**. Artinya, cukup dengan satu pangkalan data dan satu server, aplikasi ini bisa disewakan dan dipakai oleh puluhan instansi/rumah sakit yang berbeda secara bersamaan tanpa datanya saling bocor atau bercampur.
> 
> **Tujuan utamanya** adalah untuk mendigitalisasi proses pelayanan publik secara *end-to-end*. Mengubah ruang tunggu yang tadinya penuh sesak dan manual, menjadi terurai dan rapi berkat penggabungan dua jalur pendaftaran (daring dan luring) ke dalam satu ekosistem antrean terpadu (*Omnichannel*).
> 
> **Alur kerjanya secara general (Sistem Utuh)** berjalan saling berkaitan antara manajemen (*Back-office*) dan pelayanan (*Front-facing*):
> 1. **Setup Awal:** *Super Admin* mendaftarkan instansi baru. Kemudian *Admin Instansi* mengatur layanan, membuka loket, dan membuat akun staf.
> 2. **Pendaftaran (Omnichannel):** Masyarakat mengambil nomor antrean. Bisa langsung cetak karcis di layar **Kiosk**, atau *booking online* dari rumah via **HP** untuk mendapat tiket digital QR Code.
> 3. **Check-in:** Pengunjung *online* yang sudah tiba di lokasi wajib menodongkan (*scan*) layar HP-nya ke mesin Kiosk sebagai konfirmasi kehadiran fisik.
> 4. **Pemanggilan (Real-time):** **Operator Loket** menekan tombol panggil. Detik itu juga (*zero-latency* via *WebSocket*), layar **TV Monitor** berubah dan suara kecerdasan buatan (*Text-to-Speech*) memanggil nomor pengunjung.
> 5. **Penyelesaian & Analitik:** Operator menyelesaikan pelayanan dengan mengambil foto wajah pengunjung lewat **Webcam** sebagai bukti validasi. Terakhir, semua data transaksi ini masuk ke dalam dasbor **Supervisor** untuk dicetak menjadi laporan statistik harian."

### Q1: Pemahaman Konsep MVC & Struktur Folder
**Pertanyaan:** *"Coba tunjukkan ke saya, jika ada pengunjung yang melakukan pendaftaran antrean secara online, di mana letak file yang memproses datanya dari awal sampai akhir?"*
**Cara Menjawab:** 
> "Baik, Pak/Bu. Aplikasi ini menggunakan arsitektur MVC (*Model-View-Controller*) bawaan Laravel. 
> 1. Pertama, permintaan pengunjung masuk melalui pintu gerbang utama yaitu berkas **`routes/web.php`**.
> 2. Dari sana, permintaan diarahkan ke *Controller* yang mengatur logika bisnisnya, yaitu di folder **`app/Http/Controllers/BookingOnlineController.php`**.
> 3. Controller ini akan berinteraksi dengan pangkalan data melalui *Model* yang ada di folder **`app/Models/Customer.php`** dan **`Queue.php`**.
> 4. Terakhir, setelah data selesai diproses, antarmuka visualnya (tiket QR) dikembalikan ke pengunjung melalui *View* yang letaknya ada di folder **`resources/views/Pages/Remoteuser/`**."

### Q2: Pemahaman API Pihak Ketiga (WhatsApp Gateway)
**Pertanyaan:** *"Aplikasi kamu kan bisa kirim kode OTP lewat WhatsApp, bagaimana cara kerjanya secara teknis? Apakah token rahasianya aman?"*
**Cara Menjawab:**
> "Sistem kami menggunakan layanan API pihak ketiga, yaitu Wablas. Untuk menjaga kebersihan kode (*Clean Code*), saya memisahkan logika pengirimannya ke dalam folder *Service* khusus, yaitu di **`app/Services/WablasService.php`**. 
> Di dalam file tersebut, sistem menggunakan *Laravel HTTP Client* untuk mengirim data *JSON* (Nomor HP & Pesan OTP) via metode *POST* ke *server* Wablas. Terkait keamanan, Token API Wablas **tidak saya tulis langsung di dalam kode (*hardcode*)**, melainkan saya sembunyikan dengan aman di dalam *environment variable* (berkas **`.env`**)."

### Q3: Keputusan Desain Arsitektur (WebSockets vs AJAX)
**Pertanyaan:** *"Kenapa kamu repot-repot menggunakan WebSocket (Laravel Reverb) untuk TV Monitor? Bukankah pakai fungsi AJAX `setInterval()` di JavaScript jauh lebih mudah untuk memperbarui layar?"*
**Cara Menjawab:**
> "Secara teknis AJAX memang lebih mudah, tapi sangat membebani server (*Server Overload*). Jika memakai AJAX setiap 1 detik, layar TV akan terus-menerus mengebom server dengan permintaan data (seolah-olah sedang me-DDoS server sendiri), padahal mungkin belum ada antrean baru.
> Oleh karena itu saya memakai arsitektur *Event-Driven* melalui **Laravel Reverb (WebSockets)**. TV Monitor sifatnya hanya diam mendengarkan (*Listening*). Ketika Operator menekan tombol panggil, server akan memicu berkas *Event* di **`app/Events/QueueUpdated.php`**, lalu server yang aktif 'mendorong' (*Push*) data baru tersebut secara sekejap ke layar TV. Hasilnya adalah latensi nol (*zero-latency*) tanpa membuat server bekerja keras."

### Q4: Keamanan Data Tingkat Lanjut (Race Condition)
**Pertanyaan:** *"Sistem kamu dipakai oleh banyak loket. Apa yang terjadi kalau Operator Loket 1 dan Loket 2 menekan tombol 'Panggil' untuk pasien A-001 secara bersamaan di detik yang sama?"*
**Cara Menjawab:**
> Di dalam *Controller* saya, saya menggunakan metode **`Cache::lock()`** (*Atomic Lock*). Metode ini bertindak layaknya pintu putar (hanya bisa dilewati 1 orang). Jika 2 operator mengklik bersamaan di milidetik yang sama, *database* akan mengunci data tersebut untuk operator yang pertama kali masuk, dan perintah dari operator kedua akan ditolak/diminta menunggu hingga kunci dilepas."

### Q5: Evaluasi Kelemahan Logis Sistem (*System Flaws & Future Work*)
**Pertanyaan:** *"Dari semua alur (*workflow*) canggih yang sudah kamu buat, menurut kamu sendiri apa kelemahan paling mencolok yang masih ada di sistem ini, dan bagaimana rencana perbaikannya?"*
**Cara Menjawab:**
> "Terima kasih atas pertanyaannya, Pak/Bu. Berdasarkan evaluasi kami, ada dua celah logis (*logical flaws*) utama yang menjadi titik berat untuk *Future Work* (Pengembangan Selanjutnya):
> 
> **Pertama, Ketergantungan Mutlak pada Kamera Kiosk (*Single Point of Failure*).**
> Saat ini pengunjung *online* wajib melakukan *check-in* menggunakan *QR Code Scanner* di Kiosk. Kelemahannya: Jika layar HP pengunjung retak, terlalu redup, atau kamera Kiosk rusak, maka pengunjung tidak bisa *check-in*. Solusi ke depannya adalah menambahkan opsi 'Input Manual Kode Booking' via *keyboard virtual* di layar sentuh Kiosk sebagai *fallback* (cadangan).
> 
> **Kedua, Batas Waktu Kedaluwarsa yang Kaku (*Hardcoded Expiration*).**
> Saat ini tiket *online* dipatok mati akan hangus dalam **30 menit**. Kelemahannya: Jika jarak rumah pasien 45 menit dari Puskesmas, mereka tidak bisa mendaftar dari rumah karena tiket pasti hangus di jalan. Solusi ke depannya, batas waktu ini harus dibuat dinamis (bisa diatur oleh Admin Instansi dari Dasbor), atau pengguna bisa langsung memilih 'Slot Jam Kedatangan' saat mendaftar *online*."

---

## 5. Alur Kerja Integrasi Wablas API (WhatsApp Gateway)

Wablas bertindak sebagai "jembatan komunikasi" yang diperintah oleh sistem SAPA lewat panggilan API untuk mengantarkan Kode Keamanan (OTP) dan Karcis Digital. Alur kerjanya terbagi ke dalam dua fase utama:

### Fase 1: Validasi Pendaftaran (Pengiriman OTP)
1. **Input Nomor (Sisi Pengunjung):** Pengunjung membuka *website* pendaftaran SAPA E-Antrian dan memasukkan nomor WhatsApp mereka.
2. **Generate Kode (Sisi Server SAPA):** Server SAPA membuat 4 digit angka acak (OTP) dan menyimpannya di *database* dengan batas waktu kedaluwarsa (misalnya 5 menit).
3. **API Request (SAPA ➔ Wablas):** Server SAPA melakukan *HTTP POST Request* ke *endpoint* API Wablas. SAPA mengirimkan data berupa `nomor tujuan` dan `isi pesan teks` (berisi kode OTP).
4. **Pengiriman Pesan (Wablas ➔ Pengunjung):** Server Wablas memproses *request* tersebut, lalu meneruskannya sebagai pesan masuk ke aplikasi WhatsApp di HP pengunjung secara *real-time*.

### Fase 2: Konfirmasi & Pengiriman Tiket Digital
1. **Verifikasi OTP (Sisi Pengunjung):** Pengunjung melihat kode di WA, lalu mengetikkan 4 digit OTP tersebut ke *website* SAPA E-Antrian.
2. **Pencetakan Tiket (Sisi Server SAPA):** Server SAPA mencocokkan OTP. Jika cocok, server langsung membuat rekam data antrean dan men-*generate* URL QR Code.
3. **API Request Kedua (SAPA ➔ Wablas):** Server SAPA kembali memanggil API Wablas. Kali ini pesan yang dikirimkan berisi teks panjang berupa Rangkuman Tiket (Nomor Antrean, Nama Instansi/Poli, dan *Link* QR Code).
4. **Tiket Diterima (Wablas ➔ Pengunjung):** Pengunjung menerima pesan WA kedua berisi karcis digital mereka. Karcis ini nantinya tinggal ditunjukkan atau di-*scan* di mesin Kiosk saat mereka tiba di lokasi pelayanan.
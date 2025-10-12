# 📚 Sistem Informasi Perpustakaan Digital Berbasis Website

Proyek ini merupakan **Sistem Informasi Perpustakaan Digital** yang dibangun menggunakan **HTML, CSS, JavaScript, PHP, dan MySQL**, dengan framework **TailwindCSS** untuk tampilan modern, interaktif, dan responsif.

---

## 🧠 Deskripsi Singkat

Sistem ini dirancang untuk mempermudah proses manajemen perpustakaan secara digital, mulai dari **pengelolaan data buku**, **data anggota**, **peminjaman**, hingga **pengembalian buku**.  
Terdapat dua role utama dalam sistem ini, yaitu:

- 👑 **Admin**: Mengelola seluruh data (buku, anggota, peminjaman, pengembalian, laporan)
- 👤 **User**: Melihat daftar buku dan melakukan peminjaman

---

## 🏗️ Teknologi yang Digunakan

| Komponen | Teknologi |
|-----------|------------|
| **Frontend** | HTML5, CSS3, TailwindCSS, JavaScript |
| **Backend** | PHP (Native) |
| **Database** | MySQL |
| **Desain Responsif** | TailwindCSS CDN |
| **Server Lokal** | XAMPP / Laragon |

---

## 📁 Struktur Folder

```
perpustakaan-digital/
│
├── index.php                 # Halaman login utama
├── register.php              # Halaman register (opsional)
├── logout.php                # Script logout
│
├── config/
│   └── database.php          # Koneksi database MySQL
│
├── assets/
│   ├── css/style.css         # Custom CSS (tambahan Tailwind)
│   ├── js/script.js          # JavaScript custom
│   └── img/                  # Folder gambar (cover buku, ikon, dll)
│
├── admin/
│   ├── dashboard.php         # Dashboard Admin
│   ├── buku/                 # CRUD Buku
│   ├── anggota/              # CRUD User/Anggota
│   ├── peminjaman/           # CRUD Peminjaman Buku
│   ├── pengembalian/         # CRUD Pengembalian Buku
│   └── laporan/              # Cetak laporan PDF/Excel
│
└── user/
    ├── dashboard.php         # Dashboard User
    ├── buku.php              # Lihat/Cari Buku
    └── peminjaman.php        # Riwayat & Form Pinjam Buku
```

---

## 🗄️ Struktur Database

**Database:** `db_perpustakaan`

| Tabel | Deskripsi |
|--------|------------|
| `admin` | Data akun admin |
| `user` | Data anggota/user |
| `buku` | Data koleksi buku |
| `peminjaman` | Data peminjaman buku oleh user |
| `detail_peminjaman` | Detail buku yang dipinjam |
| `pengembalian` | Data pengembalian buku |

---

## 🚀 Cara Menjalankan

1. **Clone atau salin folder project** ke `htdocs` (jika menggunakan XAMPP)
2. **Import database**  
   - Buka `phpMyAdmin`
   - Buat database baru: `db_perpustakaan`
   - Import file `db_perpustakaan.sql`
3. **Jalankan server**
   - Aktifkan Apache & MySQL dari XAMPP
4. **Akses di browser**
   ```
   http://localhost/perpustakaan-digital/
   ```

---

## 👤 Akun Default

| Role | Username | Password |
|------|-----------|-----------|
| Admin | admin | 12 |
| User | admin | asd |

---

## ✨ Fitur Utama

- 🔐 Login & Register (terpisah antara admin dan user)
- 📚 CRUD Data Buku
- 👥 CRUD Data Anggota (oleh admin)
- 📖 CRUD Peminjaman Buku
- 📦 CRUD Pengembalian Buku
- 🧾 Laporan (PDF/Excel)
- 🖥️ Tampilan modern & responsif (TailwindCSS)
- 🚪 Logout dan proteksi akses halaman

---

## 📜 Lisensi

Proyek ini dibuat untuk keperluan **Tugas Akhir (TA)** / **Uji Kompetensi Keahlian (UKK)** siswa SMK jurusan **Rekayasa Perangkat Lunak (RPL)**.  
Diperbolehkan untuk dipelajari dan dimodifikasi untuk kepentingan pembelajaran.

---

## 💡 Pengembang

**Nama:** _Muhammad Panca Raditya Pamungkas_  
**Kelas:** XI RPL  
**Sekolah:** SMK PGRI 3 MALANG  
**Tahun:** 2025

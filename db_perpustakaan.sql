DROP DATABASE IF EXISTS db_perpustakaan;
CREATE DATABASE db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_perpustakaan;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
START TRANSACTION;

-- ==============================
-- TABEL: anggota
-- ==============================
CREATE TABLE `anggota` (
  `id_anggota` INT NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `alamat` TEXT,
  `no_handphone` VARCHAR(15) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('aktif','non-aktif') DEFAULT 'aktif',
  `tanggal_daftar` DATE DEFAULT NULL,
  PRIMARY KEY (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================
-- TABEL: buku
-- ==============================
CREATE TABLE `buku` (
  `buku_id` INT NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(100) NOT NULL,
  `pengarang` VARCHAR(100) DEFAULT NULL,
  `penerbit` VARCHAR(100) DEFAULT NULL,
  `tahun_terbit` YEAR DEFAULT NULL,
  `jumlah_stok` INT DEFAULT 0,
  PRIMARY KEY (`buku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `buku` (`buku_id`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `jumlah_stok`) VALUES
(1, 'My Boyfriends Wedding Dress', 'Kim Eun Jeong', 'Haru', '2009', 5);

-- ==============================
-- TABEL: user
-- ==============================
CREATE TABLE `user` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(100) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `no_handphone` VARCHAR(15) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('admin','user') DEFAULT 'user',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `no_handphone`, `email`, `role`) VALUES
(4, 'admin', 'c4ca4238a0b923820dcc509a6f75849b', 'Administrator', '081234567890', 'admin@library.com', 'admin'),
(6, 'Almira', 'c4ca4238a0b923820dcc509a6f75849b', 'Resdina Sinaga', '088294638214', 'nishikawaspiker4@gmail.com', 'user');

-- ==============================
-- TABEL: peminjaman
-- ==============================
CREATE TABLE `peminjaman` (
  `id_peminjaman` INT NOT NULL AUTO_INCREMENT,
  `id_anggota` INT NOT NULL,
  `tanggal_pinjam` DATE DEFAULT NULL,
  `tanggal_kembali` DATE DEFAULT NULL,
  `status_peminjaman` ENUM('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  `denda` DECIMAL(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id_peminjaman`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================
-- TABEL: detail_peminjaman
-- ==============================
CREATE TABLE `detail_peminjaman` (
  `detail_id` INT NOT NULL AUTO_INCREMENT,
  `id_peminjaman` INT NOT NULL,
  `id_buku` INT NOT NULL,
  `jumlah` INT DEFAULT 1,
  PRIMARY KEY (`detail_id`),
  KEY `id_peminjaman` (`id_peminjaman`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`buku_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================
-- TABEL: pengembalian
-- ==============================
CREATE TABLE `pengembalian` (
  `id_pengembalian` INT NOT NULL AUTO_INCREMENT,
  `id_peminjaman` INT NOT NULL,
  `tanggal_pengembalian` DATE DEFAULT NULL,
  `denda` DECIMAL(10,2) DEFAULT 0.00,
  `keterangan` TEXT,
  PRIMARY KEY (`id_pengembalian`),
  KEY `id_peminjaman` (`id_peminjaman`),
  CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================
-- TABEL: log_aktivitas
-- ==============================
CREATE TABLE `log_aktivitas` (
  `id_log` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `aktivitas` VARCHAR(255) DEFAULT NULL,
  `waktu` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu`) VALUES
(1, 6, 'Melakukan Login', '2025-10-21 19:03:15'),
(2, 4, 'Melakukan Login', '2025-10-21 19:03:20');

COMMIT;

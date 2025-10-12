<?php 

$localhost = 'localhost';
$username = 'root';
$password = '1';
$db_nama = 'db_perpustakaan';

$koneksi = mysqli_connect($localhost, $username, $password, $db_nama);

// Logika pengecekan apakah database berhasil tersambung atau tidak
if(!$koneksi) {
    // echo 'Gagal menyambungkan database';
} else {
    // echo 'Berhasil menyambungkan ke database';
}
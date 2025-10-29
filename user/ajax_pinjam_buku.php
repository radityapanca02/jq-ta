<?php
// ajax_pinjam_buku.php - Handle AJAX request untuk peminjaman buku
include '../config/koneksi.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session dengan output buffering
ob_start();
session_start();
ob_end_clean();

header('Content-Type: application/json');

// Cek session dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login sebagai user']);
    exit;
}

// Validasi input
if (!isset($_GET['buku_id']) || empty($_GET['buku_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
    exit;
}

$id_user = $_SESSION['user_id'];
$buku_id = intval($_GET['buku_id']);

try {
    // Cek stok buku
    $check_stock = mysqli_prepare($koneksi, "SELECT jumlah_stok, judul FROM buku WHERE buku_id = ?");
    mysqli_stmt_bind_param($check_stock, 'i', $buku_id);
    mysqli_stmt_execute($check_stock);
    $result = mysqli_stmt_get_result($check_stock);
    $buku = mysqli_fetch_assoc($result);

    if (!$buku) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
        exit;
    }

    if ($buku['jumlah_stok'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Buku sedang tidak tersedia']);
        exit;
    }

    // Hitung tanggal kembali (14 hari dari sekarang)
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+14 days'));

    // Insert data peminjaman
    $insert_query = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status_peminjaman) 
                VALUES (?, ?, ?, ?, 'dipinjam')";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    mysqli_stmt_bind_param($stmt, 'iiss', $id_user, $buku_id, $tanggal_pinjam, $tanggal_kembali);

    if (mysqli_stmt_execute($stmt)) {
        // Update stok buku
        $update_stock = mysqli_prepare($koneksi, "UPDATE buku SET jumlah_stok = jumlah_stok - 1 WHERE buku_id = ?");
        mysqli_stmt_bind_param($update_stock, 'i', $buku_id);
        mysqli_stmt_execute($update_stock);

        // Log aktivitas
        $aktivitas = "Meminjam buku: " . $buku['judul'];
        $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($log_stmt, 'is', $id_user, $aktivitas);
        mysqli_stmt_execute($log_stmt);

        echo json_encode([
            'success' => true,
            'message' => 'Buku berhasil dipinjam! Silakan cek halaman peminjaman.'
        ]);
    } else {
        throw new Exception('Gagal menyimpan data peminjaman');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
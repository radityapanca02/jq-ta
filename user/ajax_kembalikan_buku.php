<?php
// ajax_kembalikan_buku.php - Handle AJAX request untuk pengembalian buku
include '../config/koneksi.php';

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
if (!isset($_GET['peminjaman_id']) || empty($_GET['peminjaman_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID peminjaman tidak valid']);
    exit;
}

$id_user = $_SESSION['user_id'];
$peminjaman_id = intval($_GET['peminjaman_id']);

try {
    // Cek apakah peminjaman milik user ini
    $check_peminjaman = mysqli_prepare($koneksi, 
        "SELECT p.*, b.judul, b.buku_id 
         FROM peminjaman p 
         JOIN buku b ON p.id_buku = b.buku_id 
         WHERE p.id_peminjaman = ? AND p.id_user = ? AND p.status_peminjaman = 'dipinjam'"
    );
    mysqli_stmt_bind_param($check_peminjaman, 'ii', $peminjaman_id, $id_user);
    mysqli_stmt_execute($check_peminjaman);
    $result = mysqli_stmt_get_result($check_peminjaman);

    if (mysqli_num_rows($result) == 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan atau sudah dikembalikan']);
        exit;
    }

    $peminjaman = mysqli_fetch_assoc($result);

    // Update status peminjaman
    $update_query = "UPDATE peminjaman SET status_peminjaman = 'dikembalikan' WHERE id_peminjaman = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 'i', $peminjaman_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update stok buku
        $update_stock = mysqli_prepare($koneksi, "UPDATE buku SET jumlah_stok = jumlah_stok + 1 WHERE buku_id = ?");
        mysqli_stmt_bind_param($update_stock, 'i', $peminjaman['buku_id']);
        mysqli_stmt_execute($update_stock);
        
        // Log aktivitas
        $aktivitas = "Mengembalikan buku: " . $peminjaman['judul'];
        $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($log_stmt, 'is', $id_user, $aktivitas);
        mysqli_stmt_execute($log_stmt);
        
        echo json_encode(['success' => true, 'message' => 'Buku berhasil dikembalikan!']);
    } else {
        throw new Exception('Gagal mengupdate status peminjaman');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
<?php
include '../../components/header.php';
include '../../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../../index.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<script>window.location.href = "../buku/index.php";</script>';
    exit;
}

$buku_id = $_GET['id'];

$query = mysqli_prepare($koneksi, "SELECT judul FROM buku WHERE buku_id = ?");
mysqli_stmt_bind_param($query, 'i', $buku_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$buku = mysqli_fetch_assoc($result);

if ($buku) {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM buku WHERE buku_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $buku_id);

    if (mysqli_stmt_execute($stmt)) {
        $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES (?, ?, NOW())");
        $aktivitas = "Menghapus buku: " . $buku['judul'] . " (ID: $buku_id)";
        mysqli_stmt_bind_param($log_stmt, 'is', $id_user, $aktivitas);
        mysqli_stmt_execute($log_stmt);
    }
}

echo '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: "success",
    title: "Berhasil Dihapus!",
    text: "Data buku berhasil dihapus.",
    timer: 2000,
    showConfirmButton: false
}).then(() => {
    window.location.href = "../buku/index.php";
});
</script>';
exit;
?>
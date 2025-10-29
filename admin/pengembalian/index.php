<?php
include '../../config/koneksi.php';
include '../../components/header.php';

// Pastikan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

// Proses pengembalian
if (isset($_GET['kembalikan'])) {
    $id = $_GET['kembalikan'];
    $hari_ini = date('Y-m-d');

    // Ambil data peminjaman
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT tanggal_pinjam, tanggal_kembali FROM peminjaman WHERE id_peminjaman='$id'"));

    // Hitung denda jika telat
    $tgl_kembali = $data['tanggal_kembali'] ?: $hari_ini;
    $selisih = (strtotime($hari_ini) - strtotime($tgl_kembali)) / (60 * 60 * 24);
    $denda = $selisih > 0 ? $selisih * 1000 : 0; // 1000 per hari

    // Update status
    mysqli_query($koneksi, "UPDATE peminjaman 
                            SET status_peminjaman='dikembalikan', tanggal_kembali='$hari_ini', denda='$denda' 
                            WHERE id_peminjaman='$id'");

            echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../pengembalian/index.php";
        </script>';
    exit;
}

// Hapus data pengembalian
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman='$id'");
            echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../pengembalian/index.php";
        </script>';
    exit;
}

// Ambil semua data
$peminjaman = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_user, b.judul AS judul_buku 
    FROM peminjaman p
    JOIN user u ON p.id_user = u.id_user
    JOIN buku b ON p.id_buku = b.buku_id
    ORDER BY p.id_peminjaman DESC
");
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Pengembalian Buku</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="p-3">#</th>
                    <th class="p-3">Nama Anggota</th>
                    <th class="p-3">Judul Buku</th>
                    <th class="p-3">Tgl Pinjam</th>
                    <th class="p-3">Tgl Kembali</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Denda (Rp)</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = mysqli_fetch_assoc($peminjaman)): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3"><?= $no++; ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['nama_user']); ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['judul_buku']); ?></td>
                        <td class="p-3"><?= $row['tanggal_pinjam']; ?></td>
                        <td class="p-3"><?= $row['tanggal_kembali'] ?: '-'; ?></td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-white text-sm 
                                <?= $row['status_peminjaman'] == 'dipinjam' ? 'bg-yellow-500' : 'bg-green-600'; ?>">
                                <?= ucfirst($row['status_peminjaman']); ?>
                            </span>
                        </td>
                        <td class="p-3"><?= number_format($row['denda'], 2, ',', '.'); ?></td>
                        <td class="p-3">
                            <?php if ($row['status_peminjaman'] == 'dipinjam'): ?>
                                <a href="?kembalikan=<?= $row['id_peminjaman']; ?>"
                                    onclick="return confirm('Tandai buku ini sebagai dikembalikan?')"
                                    class="text-green-600 hover:underline mr-2">
                                    <i class="fas fa-undo"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500 italic">Selesai</span>
                            <?php endif; ?>
                            <a href="?hapus=<?= $row['id_peminjaman']; ?>" onclick="return confirm('Hapus data ini?')"
                                class="text-red-600 hover:underline ml-2">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
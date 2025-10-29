<?php
include '../../config/koneksi.php';
include '../../components/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

// Ambil filter tanggal jika ada
$filter_mulai = $_GET['mulai'] ?? '';
$filter_selesai = $_GET['selesai'] ?? '';

$query = "SELECT p.*, u.nama AS nama_user, b.judul AS judul_buku 
          FROM peminjaman p
          JOIN user u ON p.id_user = u.id_user
          JOIN buku b ON p.id_buku = b.buku_id";

if ($filter_mulai && $filter_selesai) {
    $query .= " WHERE p.tanggal_pinjam BETWEEN '$filter_mulai' AND '$filter_selesai'";
}

$query .= " ORDER BY p.tanggal_pinjam DESC";
$data = mysqli_query($koneksi, $query);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📊 Laporan Peminjaman</h1>
        <a href="cetak.php<?= ($filter_mulai && $filter_selesai) ? "?mulai=$filter_mulai&selesai=$filter_selesai" : "" ?>"
           target="_blank"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white shadow rounded-lg p-4 mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-1">Tanggal Mulai</label>
            <input type="date" name="mulai" value="<?= htmlspecialchars($filter_mulai); ?>"
                class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1">Tanggal Selesai</label>
            <input type="date" name="selesai" value="<?= htmlspecialchars($filter_selesai); ?>"
                class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            <!-- 🔄 Tombol Reset Filter -->
            <a href="index.php"
                class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-undo mr-2"></i>Reset
            </a>
        </div>
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-blue-700 text-white">
                    <th class="p-3">#</th>
                    <th class="p-3">Nama Anggota</th>
                    <th class="p-3">Judul Buku</th>
                    <th class="p-3">Tanggal Pinjam</th>
                    <th class="p-3">Tanggal Kembali</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($data) > 0):
                    while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3"><?= $no++; ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['nama_user']); ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['judul_buku']); ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['tanggal_kembali']); ?></td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    <?= $row['status_peminjaman'] == 'dipinjam' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'; ?>">
                                    <?= ucfirst($row['status_peminjaman']); ?>
                                </span>
                            </td>
                            <td class="p-3 text-right"><?= number_format($row['denda'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">Tidak ada data ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

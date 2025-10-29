<?php
include '../../config/koneksi.php';
include '../../components/header.php';

// Pastikan hanya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

// Tambah peminjaman
if (isset($_POST['tambah'])) {
    $id_user = $_POST['id_user'];
    $id_buku = $_POST['id_buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status_peminjaman = $_POST['status_peminjaman'];
    $denda = $_POST['denda'] ?? 0;

    mysqli_query($koneksi, "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status_peminjaman, denda)
                            VALUES ('$id_user', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', '$status_peminjaman', '$denda')");
        echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../peminjaman/index.php";
        </script>';
    exit;
}

// Edit peminjaman
if (isset($_POST['edit'])) {
    $id = $_POST['id_peminjaman'];
    $id_user = $_POST['id_user'];
    $id_buku = $_POST['id_buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status_peminjaman = $_POST['status_peminjaman'];
    $denda = $_POST['denda'];

    mysqli_query($koneksi, "UPDATE peminjaman SET 
        id_user='$id_user', 
        id_buku='$id_buku',
        tanggal_pinjam='$tanggal_pinjam', 
        tanggal_kembali='$tanggal_kembali', 
        status_peminjaman='$status_peminjaman',
        denda='$denda' 
        WHERE id_peminjaman='$id'");
        echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../peminjaman/index.php";
        </script>';
    exit;
}

// Hapus peminjaman
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman='$id'");
        echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../peminjaman/index.php";
        </script>';
    exit;
}

// Ambil data edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"));
}

// Ambil semua data
$peminjaman = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_user, b.judul AS judul_buku 
    FROM peminjaman p
    JOIN user u ON p.id_user = u.id_user
    JOIN buku b ON p.id_buku = b.buku_id
    ORDER BY p.id_peminjaman DESC
");

// Dropdown data
$anggota = mysqli_query($koneksi, "SELECT * FROM user WHERE role='user'");
$buku = mysqli_query($koneksi, "SELECT * FROM buku");
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Peminjaman</h1>
        <a href="?tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Peminjaman
        </a>
    </div>

    <?php if (isset($_GET['tambah']) || isset($_GET['edit'])): ?>
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <?= isset($_GET['edit']) ? 'Edit Data Peminjaman' : 'Tambah Peminjaman Baru' ?>
            </h3>

            <form method="POST" class="space-y-4">
                <?php if ($editData): ?>
                    <input type="hidden" name="id_peminjaman" value="<?= $editData['id_peminjaman'] ?>">
                <?php endif; ?>

                <!-- Anggota -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Nama Anggota</label>
                    <select name="id_user" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php while ($u = mysqli_fetch_assoc($anggota)): ?>
                            <option value="<?= $u['id_user']; ?>" <?= isset($editData) && $editData['id_user'] == $u['id_user'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($u['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Buku -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Judul Buku</label>
                    <select name="id_buku" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="">-- Pilih Buku --</option>
                        <?php while ($b = mysqli_fetch_assoc($buku)): ?>
                            <option value="<?= $b['buku_id']; ?>" <?= isset($editData) && $editData['id_buku'] == $b['buku_id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($b['judul']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" class="w-full border rounded-lg px-3 py-2"
                            value="<?= $editData['tanggal_pinjam'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" class="w-full border rounded-lg px-3 py-2"
                            value="<?= $editData['tanggal_kembali'] ?? ''; ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Status Peminjaman</label>
                    <select name="status_peminjaman"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="dipinjam" <?= (isset($editData) && $editData['status_peminjaman'] == 'dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                        <option value="dikembalikan" <?= (isset($editData) && $editData['status_peminjaman'] == 'dikembalikan') ? 'selected' : ''; ?>>Dikembalikan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Denda (Rp)</label>
                    <input type="number" step="0.01" name="denda" class="w-full border rounded-lg px-3 py-2"
                        value="<?= $editData['denda'] ?? '0.00'; ?>">
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Batal</a>
                    <button type="submit" name="<?= isset($_GET['edit']) ? 'edit' : 'tambah' ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Tabel Data -->
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
                                <span
                                    class="px-2 py-1 rounded text-white text-sm <?= $row['status_peminjaman'] == 'dipinjam' ? 'bg-yellow-500' : 'bg-green-600'; ?>">
                                    <?= ucfirst($row['status_peminjaman']); ?>
                                </span>
                            </td>
                            <td class="p-3"><?= number_format($row['denda'], 2, ',', '.'); ?></td>
                            <td class="p-3">
                                <a href="?edit=<?= $row['id_peminjaman']; ?>" class="text-blue-600 hover:underline mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?hapus=<?= $row['id_peminjaman']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                    class="text-red-600 hover:underline">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
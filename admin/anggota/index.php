<?php
include '../../config/koneksi.php';
include '../../components/header.php';
// session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

// Tambah anggota
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];

    mysqli_query($koneksi, "INSERT INTO user (nama, username, password, email, role) VALUES ('$nama','$username','$password','$email','user')");
    echo '<script>
            alert("Data berhasil ditambahkan");
            window.location.href = "../anggota/index.php";
        </script>';
    exit;
}

// Edit anggota
if (isset($_POST['edit'])) {
    $id = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? md5($_POST['password']) : null;

    if ($password) {
        mysqli_query($koneksi, "UPDATE user SET nama='$nama', username='$username', email='$email', password='$password' WHERE id_user='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE user SET nama='$nama', username='$username', email='$email' WHERE id_user='$id'");
    }

    echo '<script>
            alert("Data berhasil diedit");
            window.location.href = "../anggota/index.php";
        </script>';
    exit;
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user='$id'");
    echo '<script>
            alert("Data berhasil dihapus");
            window.location.href = "../anggota/index.php";
        </script>';
    exit;
}

// Ambil data untuk form edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id'"));
}

// Ambil semua data user
$anggota = mysqli_query($koneksi, "SELECT * FROM user WHERE role='user'");
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Anggota</h1>
        <a href="?tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Anggota
        </a>
    </div>

    <?php if (isset($_GET['tambah']) || isset($_GET['edit'])): ?>
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <?= isset($_GET['edit']) ? 'Edit Data Anggota' : 'Tambah Anggota Baru' ?>
            </h3>

            <form method="POST" class="space-y-4">
                <?php if ($editData): ?>
                    <input type="hidden" name="id_user" value="<?= $editData['id_user'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        value="<?= $editData['nama'] ?? '' ?>" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Username</label>
                    <input type="text" name="username"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        value="<?= $editData['username'] ?? '' ?>" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        value="<?= $editData['email'] ?? '' ?>" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Password
                        <?= isset($_GET['edit']) ? '(isi jika ingin ganti)' : '' ?></label>
                    <input type="password" name="password"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan password">
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
        <!-- Tabel Anggota -->
        <div class="bg-white shadow rounded-lg p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="p-3">#</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Username</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($anggota)): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3"><?= $no++; ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['nama']); ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['username']); ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['email']); ?></td>
                            <td class="p-3">
                                <a href="?edit=<?= $row['id_user']; ?>" class="text-blue-600 hover:underline mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?hapus=<?= $row['id_user']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus anggota ini?')"
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
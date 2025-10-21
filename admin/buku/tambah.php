<?php
$page_title = "Tambah Buku";
include '../../components/header.php';

include '../../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>window.location.href = "../../index.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];

$error = null;
$success = null;
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $jumlah_stok = $_POST['jumlah_stok'];

    $stmt = mysqli_prepare($koneksi, "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, jumlah_stok) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssi', $judul, $pengarang, $penerbit, $tahun, $jumlah_stok);

    if (mysqli_stmt_execute($stmt)) {
        $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES (?, ?, NOW())");
        $aktivitas = "Menambah buku: $judul";
        mysqli_stmt_bind_param($log_stmt, 'is', $id_user, $aktivitas);
        mysqli_stmt_execute($log_stmt);

        $success = "Buku berhasil ditambahkan!";
        echo '<script>
            setTimeout(function() {
                window.location.href = "../buku/index.php";
            }, 2000);
        </script>';
    } else {
        $error = "Gagal menyimpan data";
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $success; ?>',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
<?php endif; ?>

<h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Buku Baru</h2>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white p-6 rounded-xl shadow-md space-y-4" onsubmit="return validateForm()">
    <div>
        <label class="block text-gray-700 mb-2">Judul Buku</label>
        <input type="text" name="judul" placeholder="Masukkan judul buku" required
            class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-gray-700 mb-2">Pengarang</label>
        <input type="text" name="pengarang" placeholder="Masukkan nama pengarang" required
            class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-gray-700 mb-2">Penerbit</label>
        <input type="text" name="penerbit" placeholder="Masukkan nama penerbit" required
            class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-gray-700 mb-2">Tahun Terbit</label>
        <input type="number" name="tahun" placeholder="Masukkan tahun terbit" required min="1900"
            max="<?php echo date('Y'); ?>"
            class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-gray-700 mb-2">Jumlah Stok</label>
        <input type="number" name="jumlah_stok" placeholder="Masukkan jumlah stok" required min="0"
            class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="flex justify-end space-x-3 pt-4">
        <a href="index.php"
            class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition duration-200">Batal</a>
        <button type="submit" name="simpan"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">Simpan</button>
    </div>
</form>

<script>
    function validateForm() {
        const judul = document.querySelector('input[name="judul"]').value.trim();
        const pengarang = document.querySelector('input[name="pengarang"]').value.trim();
        const penerbit = document.querySelector('input[name="penerbit"]').value.trim();
        const tahun = document.querySelector('input[name="tahun"]').value;
        const jumlahStok = document.querySelector('input[name="jumlah_stok"]').value;

        if (!judul || !pengarang || !penerbit || !tahun || !jumlahStok) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Harap isi semua field yang wajib diisi!',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        if (tahun < 1900 || tahun > new Date().getFullYear()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tahun Tidak Valid',
                text: 'Tahun terbit harus antara 1900 dan <?php echo date('Y'); ?>!',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        if (jumlahStok < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Tidak Valid',
                text: 'Jumlah stok tidak boleh negatif!',
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        return Swal.fire({
            title: 'Yakin Tambah Data?',
            text: "Apakah Anda yakin ingin menambahkan buku baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tambah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            return result.isConfirmed;
        });
    }
</script>

<?php include '../../components/footer.php'; ?>
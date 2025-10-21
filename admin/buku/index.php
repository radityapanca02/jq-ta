<?php
$page_title = "Kelola Buku";
include '../../components/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Buku 📚</h2>
    <a href="tambah.php"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Buku
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead>
            <tr class="bg-blue-600 text-white text-left">
                <th class="p-3">#</th>
                <th class="p-3">Judul</th>
                <th class="p-3">Pengarang</th>
                <th class="p-3">Penerbit</th>
                <th class="p-3">Tahun</th>
                <th class="p-3">Jumlah Stok</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $result = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY buku_id DESC");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "
        <tr class='border-b hover:bg-gray-50'>
            <td class='p-3'>{$no}</td>
            <td class='p-3'>{$row['judul']}</td>
            <td class='p-3'>{$row['pengarang']}</td>
            <td class='p-3'>{$row['penerbit']}</td>
            <td class='p-3'>{$row['tahun_terbit']}</td>
            <td class='p-3'>{$row['jumlah_stok']}</td>
            <td class='p-3 text-center'>
                <a href='edit.php?id={$row['buku_id']}' class='text-blue-600 hover:text-blue-800 mr-3'><i class='fas fa-edit'></i></a>
                <a href='hapus.php?id={$row['buku_id']}' class='text-red-600 hover:text-red-800' onclick='return confirm(\"Yakin hapus buku ini?\")'><i class='fas fa-trash'></i></a>
            </td>
        </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../../components/footer.php'; ?>
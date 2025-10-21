<?php
// buku.php - Halaman Koleksi Buku untuk User
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? $username;

// Query untuk mengambil data buku dengan filter dan pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$query = "SELECT * FROM buku WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (judul LIKE ? OR pengarang LIKE ? OR penerbit LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= 'sss';
}

if (!empty($kategori) && $kategori != 'semua') {
    if ($kategori == 'tersedia') {
        $query .= " AND jumlah_stok > 0";
    } elseif ($kategori == 'habis') {
        $query .= " AND jumlah_stok = 0";
    }
}

$query .= " ORDER BY judul ASC";

// Eksekusi query
$stmt = mysqli_prepare($koneksi, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Hitung total buku
$total_buku = mysqli_num_rows($result);

// Hitung buku tersedia
$buku_tersedia = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku WHERE jumlah_stok > 0")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku - Sistem Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }

            .overlay.active {
                display: block;
            }
        }

        .book-cover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .loading {
            display: none;
        }

        .pinjam-btn {
            cursor: pointer;
        }

        .pinjam-btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div class="flex h-screen">
        <!-- Sidebar (sama seperti dashboard) -->
        <div class="sidebar w-64 bg-green-800 text-white flex flex-col">
            <div class="p-6 border-b border-green-700">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-book-reader mr-2"></i>
                    Perpustakaan Digital
                </h1>
                <p class="text-green-200 text-sm mt-1">Area Anggota</p>
            </div>

            <div class="p-4 border-b border-green-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium"><?= htmlspecialchars($nama); ?></p>
                        <p class="text-xs text-green-200">Anggota Perpustakaan</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="buku.php" class="flex items-center p-3 rounded-lg bg-green-700">
                            <i class="fas fa-book w-6"></i>
                            <span>Lihat Koleksi Buku</span>
                        </a>
                    </li>
                    <li>
                        <a href="peminjaman.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-hand-holding w-6"></i>
                            <span>Peminjaman Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="profil.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-user-edit w-6"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-green-700">
                <a href="../logout.php"
                    class="flex items-center p-3 rounded-lg hover:bg-green-700 transition text-red-300 hover:text-white">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menu-toggle" class="md:hidden text-gray-500 hover:text-green-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800 ml-4">Koleksi Buku Perpustakaan</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Jelajahi Koleksi Buku 📚</h2>
                    <p class="text-gray-600">Temukan buku favorit Anda dari koleksi perpustakaan kami.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Koleksi</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($total_buku); ?>
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <p class="text-blue-600 text-sm mt-2">
                            <i class="fas fa-bookmark mr-1"></i>Semua buku perpustakaan
                        </p>
                    </div>

                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Tersedia</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($buku_tersedia); ?>
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-check mr-1"></i>Siap dipinjam
                        </p>
                    </div>

                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Telah Dipinjam</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                    <?= number_format($total_buku - $buku_tersedia); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                        </div>
                        <p class="text-purple-600 text-sm mt-2">
                            <i class="fas fa-users mr-1"></i>Sedang dipinjam anggota
                        </p>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cari & Filter Buku</h3>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Pencarian</label>
                            <input type="text" name="search" placeholder="Cari judul, pengarang, atau penerbit..."
                                value="<?= htmlspecialchars($search); ?>"
                                class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Status Ketersediaan</label>
                            <select name="kategori"
                                class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="semua" <?= $kategori == 'semua' ? 'selected' : ''; ?>>Semua Buku</option>
                                <option value="tersedia" <?= $kategori == 'tersedia' ? 'selected' : ''; ?>>Tersedia
                                </option>
                                <option value="habis" <?= $kategori == 'habis' ? 'selected' : ''; ?>>Sedang Dipinjam
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end space-x-3">
                            <button type="submit"
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 w-full">
                                <i class="fas fa-search mr-2"></i>Cari Buku
                            </button>
                            <a href="buku.php"
                                class="bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200">
                                <i class="fas fa-refresh mr-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Books Grid -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Buku</h3>
                        <span class="text-sm text-gray-500">
                            Menampilkan <?= number_format($total_buku); ?> buku
                        </span>
                    </div>

                    <?php if ($total_buku > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="books-grid">
                            <?php while ($buku = mysqli_fetch_assoc($result)): 
                                // Escape judul buku untuk JavaScript dengan menghapus single quote
                                $judul_escaped = htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8');
                                $judul_js = str_replace("'", "\\'", $buku['judul']);
                                $judul_js = str_replace('"', '\\"', $judul_js);
                            ?>
                                <div class="card bg-white border border-gray-200 rounded-xl overflow-hidden">
                                    <!-- Book Cover -->
                                    <div class="book-cover h-32 flex items-center justify-center text-white">
                                        <i class="fas fa-book text-4xl"></i>
                                    </div>

                                    <!-- Book Info -->
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-800 mb-2 line-clamp-2"
                                            title="<?= $judul_escaped; ?>">
                                            <?= $judul_escaped; ?>
                                        </h4>

                                        <div class="space-y-1 text-sm text-gray-600 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-user-edit mr-2 text-green-600"></i>
                                                <span><?= htmlspecialchars($buku['pengarang']); ?></span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                                <span><?= htmlspecialchars($buku['penerbit']); ?></span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar mr-2 text-purple-600"></i>
                                                <span>Tahun: <?= $buku['tahun_terbit']; ?></span>
                                            </div>
                                        </div>

                                        <!-- Status & Action -->
                                        <div class="flex justify-between items-center mt-4">
                                            <span
                                                class="<?= $buku['jumlah_stok'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> px-3 py-1 rounded-full text-xs font-medium">
                                                <?= $buku['jumlah_stok'] > 0 ? 'Tersedia' : 'Dipinjam'; ?>
                                            </span>

                                            <?php if ($buku['jumlah_stok'] > 0): ?>
                                                <button
                                                    onclick="pinjamBuku(<?= $buku['buku_id']; ?>, '<?= $judul_js; ?>')"
                                                    class="pinjam-btn bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 text-sm">
                                                    <i class="fas fa-hand-holding mr-1"></i>Pinjam
                                                </button>
                                            <?php else: ?>
                                                <button disabled
                                                    class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm cursor-not-allowed">
                                                    <i class="fas fa-clock mr-1"></i>Menunggu
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">Tidak ada buku ditemukan</h4>
                            <p class="text-gray-500">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <div class="overlay"></div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        });

        document.querySelector('.overlay').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });

        function pinjamBuku(bukuId, judulBuku) {
            console.log('Meminjam buku:', bukuId, judulBuku); // Debug log
            
            Swal.fire({
                title: 'Pinjam Buku?',
                html: `Apakah Anda yakin ingin meminjam buku:<br><strong>"${judulBuku}"</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Pinjam!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    try {
                        const response = await fetch(`ajax_pinjam_buku.php?buku_id=${bukuId}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        return data;
                    } catch (error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                        return null;
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    if (result.value.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: result.value.message,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                        }).then(() => {
                            window.location.href = 'peminjaman.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: result.value.message,
                            icon: 'error',
                            confirmButtonColor: '#10B981',
                        });
                    }
                }
            });
        }

        // Debug: Cek apakah semua button bisa diklik
        document.addEventListener('DOMContentLoaded', function () {
            const pinjamButtons = document.querySelectorAll('.pinjam-btn');
            console.log('Total button pinjam:', pinjamButtons.length);
            
            pinjamButtons.forEach((button, index) => {
                console.log(`Button ${index + 1}:`, {
                    onclick: button.getAttribute('onclick'),
                    disabled: button.disabled,
                    style: window.getComputedStyle(button).cursor
                });
            });

            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
</body>

</html>
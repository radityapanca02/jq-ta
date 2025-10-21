<?php
// peminjaman.php - Halaman Peminjaman User
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? $username;

// Query peminjaman aktif user
$peminjaman_aktif = mysqli_query($koneksi, 
    "SELECT p.*, b.judul, b.pengarang, b.penerbit 
     FROM peminjaman p 
     JOIN buku b ON p.id_buku = b.buku_id 
     WHERE p.id_user = '$id_user' AND p.status_peminjaman = 'dipinjam' 
     ORDER BY p.tanggal_pinjam DESC"
);

// Query riwayat peminjaman
$riwayat_peminjaman = mysqli_query($koneksi, 
    "SELECT p.*, b.judul, b.pengarang, b.penerbit 
     FROM peminjaman p 
     JOIN buku b ON p.id_buku = b.buku_id 
     WHERE p.id_user = '$id_user' AND p.status_peminjaman = 'dikembalikan' 
     ORDER BY p.tanggal_pinjam DESC 
     LIMIT 10"
);

// Hitung statistik
$total_peminjaman_aktif = mysqli_num_rows($peminjaman_aktif);
$total_riwayat = mysqli_num_rows($riwayat_peminjaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - Sistem Perpustakaan</title>
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

        .table-row:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-dipinjam {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-dikembalikan {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-terlambat {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="flex h-screen">
        <!-- Sidebar -->
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
                        <a href="buku.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-book w-6"></i>
                            <span>Lihat Koleksi Buku</span>
                        </a>
                    </li>
                    <li>
                        <a href="peminjaman.php" class="flex items-center p-3 rounded-lg bg-green-700">
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
                <a href="../logout.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition text-red-300 hover:text-white">
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
                        <h1 class="text-xl font-bold text-gray-800 ml-4">Peminjaman Saya</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <?php echo date('d F Y'); ?>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Peminjaman Buku Saya 📖</h2>
                    <p class="text-gray-600">Kelola semua peminjaman buku Anda di satu tempat.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Peminjaman Aktif</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($total_peminjaman_aktif); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>
                        <p class="text-blue-600 text-sm mt-2">
                            <i class="fas fa-clock mr-1"></i>Sedang dipinjam
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Riwayat</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($total_riwayat); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-history"></i>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-check mr-1"></i>Sudah dikembalikan
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Batas Waktu</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                    <?php 
                                        $today = date('Y-m-d');
                                        $tenggat_count = 0;
                                        mysqli_data_seek($peminjaman_aktif, 0);
                                        while ($p = mysqli_fetch_assoc($peminjaman_aktif)) {
                                            if (strtotime($p['tanggal_kembali']) < strtotime($today)) {
                                                $tenggat_count++;
                                            }
                                        }
                                        mysqli_data_seek($peminjaman_aktif, 0);
                                        echo number_format($tenggat_count);
                                    ?>
                                </h3>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <p class="text-purple-600 text-sm mt-2">
                            <i class="fas fa-warning mr-1"></i>Perlu perhatian
                        </p>
                    </div>
                </div>

                <!-- Peminjaman Aktif -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Peminjaman Aktif</h3>
                        <span class="text-sm text-gray-500">
                            <?= number_format($total_peminjaman_aktif); ?> buku sedang dipinjam
                        </span>
                    </div>
                    
                    <?php if ($total_peminjaman_aktif > 0): ?>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Judul Buku</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Pengarang</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Tanggal Pinjam</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Batas Kembali</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Status</th>
                                        <th class="py-3 px-4 text-center text-sm font-medium text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php while ($peminjaman = mysqli_fetch_assoc($peminjaman_aktif)): 
                                        $is_overdue = strtotime($peminjaman['tanggal_kembali']) < strtotime(date('Y-m-d'));
                                        $days_remaining = ceil((strtotime($peminjaman['tanggal_kembali']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                                    ?>
                                        <tr class="table-row">
                                            <td class="py-4 px-4">
                                                <div>
                                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($peminjaman['judul']); ?></p>
                                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($peminjaman['penerbit']); ?></p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-gray-600"><?= htmlspecialchars($peminjaman['pengarang']); ?></td>
                                            <td class="py-4 px-4 text-gray-600">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-plus mr-2 text-blue-500"></i>
                                                    <?= date('d M Y', strtotime($peminjaman['tanggal_pinjam'])); ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center <?= $is_overdue ? 'text-red-600 font-medium' : 'text-gray-600'; ?>">
                                                    <i class="fas fa-calendar-check mr-2 <?= $is_overdue ? 'text-red-500' : 'text-green-500'; ?>"></i>
                                                    <?= date('d M Y', strtotime($peminjaman['tanggal_kembali'])); ?>
                                                    <?php if ($is_overdue): ?>
                                                        <span class="text-red-500 text-xs ml-2">(Terlambat <?= abs($days_remaining); ?> hari)</span>
                                                    <?php elseif ($days_remaining <= 3): ?>
                                                        <span class="text-yellow-500 text-xs ml-2">(<?= $days_remaining; ?> hari lagi)</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="status-badge <?= $is_overdue ? 'status-terlambat' : 'status-dipinjam'; ?>">
                                                    <?= $is_overdue ? 'Terlambat' : 'Dipinjam'; ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <button onclick="kembalikanBuku(<?= $peminjaman['id_peminjaman']; ?>, '<?= htmlspecialchars($peminjaman['judul']); ?>')" 
                                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 text-sm font-medium">
                                                    <i class="fas fa-undo-alt mr-1"></i>Kembalikan
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-4 text-gray-400"></i>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">Belum ada peminjaman aktif</h4>
                            <p class="text-gray-500 mb-6">Pinjam buku terlebih dahulu di halaman koleksi buku</p>
                            <a href="buku.php" class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                                <i class="fas fa-book mr-2"></i>Jelajahi Buku
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Riwayat Peminjaman -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Riwayat Peminjaman</h3>
                        <span class="text-sm text-gray-500">
                            <?= number_format($total_riwayat); ?> buku sudah dikembalikan
                        </span>
                    </div>
                    
                    <?php if ($total_riwayat > 0): ?>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Judul Buku</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Pengarang</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Tanggal Pinjam</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Tanggal Kembali</th>
                                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php while ($riwayat = mysqli_fetch_assoc($riwayat_peminjaman)): ?>
                                        <tr class="table-row">
                                            <td class="py-4 px-4">
                                                <div>
                                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($riwayat['judul']); ?></p>
                                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($riwayat['penerbit']); ?></p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-gray-600"><?= htmlspecialchars($riwayat['pengarang']); ?></td>
                                            <td class="py-4 px-4 text-gray-600">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-plus mr-2 text-blue-500"></i>
                                                    <?= date('d M Y', strtotime($riwayat['tanggal_pinjam'])); ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-gray-600">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-check mr-2 text-green-500"></i>
                                                    <?= date('d M Y', strtotime($riwayat['tanggal_kembali'])); ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="status-badge status-dikembalikan">
                                                    Dikembalikan
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-history text-4xl mb-4 text-gray-400"></i>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">Belum ada riwayat peminjaman</h4>
                            <p class="text-gray-500">Riwayat akan muncul setelah Anda mengembalikan buku</p>
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
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        });
        
        document.querySelector('.overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });

        function kembalikanBuku(peminjamanId, judulBuku) {
            Swal.fire({
                title: 'Kembalikan Buku?',
                html: `Apakah Anda yakin ingin mengembalikan buku:<br><strong>"${judulBuku}"</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Kembalikan!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    try {
                        const response = await fetch(`ajax_kembalikan_buku.php?peminjaman_id=${peminjamanId}`);
                        
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
                            window.location.reload();
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
    </script>
</body>
</html>
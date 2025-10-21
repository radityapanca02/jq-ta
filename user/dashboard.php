<?php
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? $username;

// Statistik
$buku_dipinjam = mysqli_query($koneksi, 
    "SELECT COUNT(*) as total FROM peminjaman 
     WHERE id_user = '$id_user' AND status_peminjaman = 'dipinjam'"
)->fetch_assoc()['total'];

$buku_dikembalikan = mysqli_query($koneksi, 
    "SELECT COUNT(*) as total FROM peminjaman 
     WHERE id_user = '$id_user' AND status_peminjaman = 'dikembalikan'"
)->fetch_assoc()['total'];

$tiga_hari_lagi = date('Y-m-d', strtotime('+3 days'));
$tenggat_waktu = mysqli_query($koneksi, 
    "SELECT COUNT(*) as total FROM peminjaman 
     WHERE id_user = '$id_user' AND status_peminjaman = 'dipinjam' 
     AND tanggal_kembali <= '$tiga_hari_lagi'"
)->fetch_assoc()['total'];

// PERBAIKAN: Query peminjaman aktif yang benar
$peminjaman_aktif = mysqli_query($koneksi, 
    "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, 
            b.judul, b.pengarang 
     FROM peminjaman p
     JOIN buku b ON p.id_buku = b.buku_id
     WHERE p.id_user = '$id_user' AND p.status_peminjaman = 'dipinjam'
     ORDER BY p.tanggal_pinjam DESC 
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Perpustakaan</title>
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
                        <a href="dashboard.php" class="flex items-center p-3 rounded-lg bg-green-700">
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
                        <h1 class="text-xl font-bold text-gray-800 ml-4">Dashboard Anggota</h1>
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
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, <?= htmlspecialchars($nama); ?>! 👋</h2>
                    <p class="text-gray-600">Anda login sebagai <span class="font-semibold text-green-600">Anggota Perpustakaan</span>. Jelajahi koleksi buku dan kelola peminjaman Anda.</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Buku Dipinjam</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($buku_dipinjam); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>
                        <p class="text-blue-600 text-sm mt-2">
                            <i class="fas fa-clock mr-1"></i>Sedang Anda pinjam
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Buku Dikembalikan</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($buku_dikembalikan); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-history mr-1"></i>Riwayat peminjaman
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Tenggat Waktu</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($tenggat_waktu); ?></h3>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-yellow-600 text-sm mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Mendekati batas
                        </p>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="buku.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 transition">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-2">
                                <i class="fas fa-search"></i>
                            </div>
                            <span class="text-sm text-center">Jelajahi Buku</span>
                        </a>
                        
                        <a href="peminjaman.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 transition">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mb-2">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <span class="text-sm text-center">Peminjaman Saya</span>
                        </a>
                        
                        <a href="peminjaman.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 transition">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mb-2">
                                <i class="fas fa-history"></i>
                            </div>
                            <span class="text-sm text-center">Riwayat Saya</span>
                        </a>
                    </div>
                </div>
                
                <!-- Recent Activity & Notifications -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Peminjaman Aktif</h3>
                        <div class="space-y-3">
                            <?php if (mysqli_num_rows($peminjaman_aktif) > 0): ?>
                                <?php while ($peminjaman = mysqli_fetch_assoc($peminjaman_aktif)): 
                                    $is_overdue = strtotime($peminjaman['tanggal_kembali']) < strtotime(date('Y-m-d'));
                                ?>
                                    <div class="flex items-start p-3 border <?= $is_overdue ? 'border-red-100 bg-red-50' : 'border-gray-100'; ?> rounded-lg">
                                        <div class="p-2 rounded-full <?= $is_overdue ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'; ?> mr-3">
                                            <i class="fas fa-book text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($peminjaman['judul']); ?></p>
                                            <p class="text-xs text-gray-600">Oleh: <?= htmlspecialchars($peminjaman['pengarang']); ?></p>
                                            <p class="text-xs <?= $is_overdue ? 'text-red-600' : 'text-blue-600'; ?> font-medium mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                Kembali: <?= date('d M Y', strtotime($peminjaman['tanggal_kembali'])); ?>
                                                <?php if ($is_overdue): ?>
                                                    <span class="text-red-500 ml-1">(Terlambat)</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-book-open text-4xl mb-3"></i>
                                    <p>Tidak ada peminjaman aktif</p>
                                    <p class="text-sm">Peminjaman Anda akan muncul di sini</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifikasi & Peringatan</h3>
                        <div class="space-y-3">
                            <?php if ($tenggat_waktu > 0): ?>
                                <div class="flex items-start p-3 border border-red-100 rounded-lg bg-red-50">
                                    <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                                        <i class="fas fa-exclamation-triangle text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Peringatan Tenggat Waktu</p>
                                        <p class="text-xs text-red-600 mt-1">
                                            Anda memiliki <?= $tenggat_waktu; ?> buku yang mendekati batas pengembalian
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($buku_dipinjam == 0): ?>
                                <div class="flex items-start p-3 border border-blue-100 rounded-lg bg-blue-50">
                                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                        <i class="fas fa-info-circle text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Mulai Meminjam Buku</p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            Jelajahi koleksi buku kami dan pinjam buku favorit Anda
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($tenggat_waktu == 0 && $buku_dipinjam > 0): ?>
                                <div class="flex items-start p-3 border border-green-100 rounded-lg bg-green-50">
                                    <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Status Baik</p>
                                        <p class="text-xs text-green-600 mt-1">
                                            Semua peminjaman Anda dalam kondisi baik
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <div class="overlay"></div>
    
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        });
        
        document.querySelector('.overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });

        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>
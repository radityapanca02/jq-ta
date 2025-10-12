<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Perpustakaan</title>
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
        <div class="sidebar w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 border-b border-blue-700">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-book-reader mr-2"></i>
                    Perpustakaan Digital
                </h1>
                <p class="text-blue-200 text-sm mt-1">Sistem Manajemen</p>
            </div>
            
            <div class="p-4 border-b border-blue-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">Admin</p>
                        <p class="text-xs text-blue-200">Administrator</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center p-3 rounded-lg bg-blue-700">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="buku/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-book w-6"></i>
                            <span>Kelola Buku</span>
                        </a>
                    </li>
                    <li>
                        <a href="anggota/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-users w-6"></i>
                            <span>Data Anggota</span>
                        </a>
                    </li>
                    <li>
                        <a href="peminjaman/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-hand-holding w-6"></i>
                            <span>Peminjaman</span>
                        </a>
                    </li>
                    <li>
                        <a href="pengembalian/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-undo-alt w-6"></i>
                            <span>Pengembalian</span>
                        </a>
                    </li>
                    <li>
                        <a href="laporan/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-chart-bar w-6"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-blue-700">
                <a href="../logout.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition text-red-300 hover:text-white">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menu-toggle" class="md:hidden text-gray-500 hover:text-blue-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800 ml-4">Dashboard Admin</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-blue-600">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, Admin! 👑</h2>
                    <p class="text-gray-600">Anda login sebagai <span class="font-semibold text-blue-600">Administrator</span>. Kelola sistem perpustakaan dengan mudah dari dashboard ini.</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Buku</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">-</h3>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Data akan terisi otomatis
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Anggota Aktif</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">-</h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Data akan terisi otomatis
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Peminjaman Aktif</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">-</h3>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Data akan terisi otomatis
                        </p>
                    </div>
                    
                    <div class="card bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Tenggat Waktu</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">-</h3>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Data akan terisi otomatis
                        </p>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="buku/tambah.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 transition">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-2">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="text-sm text-center">Tambah Buku</span>
                        </a>
                        
                        <a href="peminjaman/tambah.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 transition">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mb-2">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <span class="text-sm text-center">Peminjaman Baru</span>
                        </a>
                        
                        <a href="anggota/tambah.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 transition">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mb-2">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="text-sm text-center">Tambah Anggota</span>
                        </a>
                        
                        <a href="laporan/index.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-yellow-50 transition">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mb-2">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <span class="text-sm text-center">Lihat Laporan</span>
                        </a>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-history text-4xl mb-3"></i>
                            <p>Tidak ada aktivitas terbaru</p>
                            <p class="text-sm">Aktivitas akan muncul di sini</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Peminjaman Mendekati Tenggat</h3>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clock text-4xl mb-3"></i>
                            <p>Tidak ada peminjaman mendekati tenggat</p>
                            <p class="text-sm">Peringatan akan muncul di sini</p>
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
    </script>
</body>
</html>
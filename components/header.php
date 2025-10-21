<?php
session_start();
include '../../config/koneksi.php';
$nama = $_SESSION['nama'] ?? 'Admin';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Perpustakaan Digital'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .card { transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .sidebar { transition: all 0.3s ease; }
        @media (max-width:768px){
            .sidebar{transform:translateX(-100%);position:fixed;z-index:50;height:100vh;}
            .sidebar.active{transform:translateX(0);}
            .overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:40;}
            .overlay.active{display:block;}
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="flex h-screen">

    <!-- Sidebar -->
    <div class="sidebar w-64 bg-blue-800 text-white flex flex-col">
        <div class="p-6 border-b border-blue-700">
            <h1 class="text-xl font-bold flex items-center">
                <i class="fas fa-book-reader mr-2"></i> Perpustakaan Digital
            </h1>
            <p class="text-blue-200 text-sm mt-1">Sistem Manajemen</p>
        </div>

        <!-- User Info -->
        <div class="p-4 border-b border-blue-700">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium"><?= htmlspecialchars($nama); ?></p>
                    <p class="text-xs text-blue-200 capitalize">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <?php if ($role === 'admin'): ?>
                    <li><a href="../dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-tachometer-alt w-6"></i>Dashboard</a></li>
                    <li><a href="../buku/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-book w-6"></i>Kelola Buku</a></li>
                    <li><a href="../anggota/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-users w-6"></i>Data Anggota</a></li>
                    <li><a href="../peminjaman/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-hand-holding w-6"></i>Peminjaman</a></li>
                    <li><a href="../pengembalian/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-undo-alt w-6"></i>Pengembalian</a></li>
                    <li><a href="../laporan/index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-chart-bar w-6"></i>Laporan</a></li>
                <?php else: ?>
                    <li><a href="../dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-home w-6"></i>Dashboard</a></li>
                    <li><a href="../buku.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-book w-6"></i>Koleksi Buku</a></li>
                    <li><a href="../peminjaman.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-hand-holding w-6"></i>Peminjaman Saya</a></li>
                    <li><a href="../log_aktivitas.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition"><i class="fas fa-history w-6"></i>Riwayat Aktivitas</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-blue-700">
            <a href="/logout.php" class="flex items-center p-3 rounded-lg hover:bg-blue-700 transition text-red-300 hover:text-white">
                <i class="fas fa-sign-out-alt w-6"></i>Keluar
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button id="menu-toggle" class="md:hidden text-gray-500 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800 ml-4"><?= $page_title ?? 'Dashboard'; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-gray-500 hover:text-blue-600"><i class="fas fa-bell"></i></button>
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-6">

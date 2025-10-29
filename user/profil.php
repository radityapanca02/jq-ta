<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

$id_user = $_SESSION['user_id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? $username;

// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "<div class='p-4 bg-red-100 text-red-600 rounded-md'>Data profil tidak ditemukan.</div>";
    exit();
}

// Update profil
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);

    $update = mysqli_query($koneksi, "UPDATE user SET nama='$nama', email='$email', username='$username' WHERE id_user='$id_user'");

    if ($update) {
        $_SESSION['nama'] = $nama;
        $success = "Profil berhasil diperbarui!";
        $user['nama'] = $nama;
        $user['email'] = $email;
        $user['username'] = $username;
    } else {
        $error = "Gagal memperbarui profil. Coba lagi nanti.";
    }
}

// Update password
if (isset($_POST['update_password'])) {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Cek password lama
    if ($password_lama === $user['password']) {
        if ($password_baru === $konfirmasi) {
            $hash = md5($password_baru);
            mysqli_query($koneksi, "UPDATE user SET password='$hash' WHERE id_user='$id_user'");
            $success = "Password berhasil diubah!";
        } else {
            $error = "Konfirmasi password tidak cocok.";
        }
    } else {
        $error = "Password lama salah.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Perpustakaan Digital</title>
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
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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
                    <i class="fas fa-book-reader mr-2"></i> Perpustakaan Digital
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
                    <li><a href="dashboard.php"
                            class="flex items-center p-3 rounded-lg hover:bg-green-700 transition"><i
                                class="fas fa-tachometer-alt w-6"></i>Dashboard</a></li>
                    <li><a href="buku.php" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition"><i
                                class="fas fa-book w-6"></i>Lihat Koleksi Buku</a></li>
                    <li><a href="peminjaman.php"
                            class="flex items-center p-3 rounded-lg hover:bg-green-700 transition"><i
                                class="fas fa-hand-holding w-6"></i>Peminjaman Saya</a></li>
                    <li><a href="profil.php" class="flex items-center p-3 rounded-lg bg-green-700"><i
                                class="fas fa-user-edit w-6"></i>Profil Saya</a></li>
                </ul>
            </nav>

            <div class="p-4 border-t border-green-700">
                <a href="../logout.php"
                    class="flex items-center p-3 rounded-lg hover:bg-green-700 transition text-red-300 hover:text-white">
                    <i class="fas fa-sign-out-alt w-6"></i><span>Keluar</span>
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
                        <h1 class="text-xl font-bold text-gray-800 ml-4">Profil Saya</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-calendar-alt mr-1"></i><?php echo date('d F Y'); ?>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6 card">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">👤 Kelola Profil</h2>

                    <?php if (isset($success)): ?>
                        <div class="p-3 bg-green-100 text-green-700 rounded mb-4"><?= $success; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="p-3 bg-red-100 text-red-700 rounded mb-4"><?= $error; ?></div>
                    <?php endif; ?>

                    <!-- Form Update Profil -->
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>"
                                required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <button type="submit" name="update"
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Simpan
                            Perubahan</button>
                    </form>

                    <hr class="my-6">

                    <!-- Form Ubah Password -->
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">🔒 Ubah Password</h3>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-1">Password Lama</label>
                            <input type="password" name="password_lama" required
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password_baru" required
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" required
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <button type="submit" name="update_password"
                            class="w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600">Ubah
                            Password</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <div class="overlay"></div>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        });
        document.querySelector('.overlay').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });
    </script>
</body>

</html>
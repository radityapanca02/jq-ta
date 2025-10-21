<?php
include('config/koneksi.php');
session_start();

// Cek jika user sudah login, redirect ke halaman sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Set semua data user ke session
        $_SESSION['user_id'] = $data['id_user']; // INI YANG PENTING!
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama'] = $data['nama'];

        // Log aktivitas login
        $id_user = $data['id_user'];
        mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_user, aktivitas, waktu) 
                               VALUES ('$id_user', 'Melakukan Login', NOW())");

        // Redirect ke halaman sesuai role
        if ($data['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Username atau password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Login Akun</h2>
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 mb-2">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" name="login"
                class="bg-blue-500 text-white w-full py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                Login
            </button>
        </form>
        <p class="text-center mt-4 text-sm text-gray-600">
            Belum punya akun? <a href="register.php" class="text-blue-600 hover:text-blue-800 font-medium">Daftar</a>
        </p>
    </div>
</body>

</html>
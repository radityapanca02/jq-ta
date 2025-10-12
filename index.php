<?php
include('config/koneksi.php');
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Cek admin
    $admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($admin) > 0) {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header("Location: admin/dashboard.php");
        exit;
    }

    // Cek user
    $user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($user) > 0) {
        $_SESSION['role'] = 'user';
        $_SESSION['username'] = $username;
        header("Location: user/dashboard.php");
        exit;
    }

    echo "<script>alert('Username atau password salah!');</script>";
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
            <input type="text" name="username" placeholder="Username" required class="w-full mb-3 p-2 border rounded">
            <input type="password" name="password" placeholder="Password" required
                class="w-full mb-3 p-2 border rounded">
            <button type="submit" name="login"
                class="bg-blue-500 text-white w-full py-2 rounded hover:bg-blue-600">Login</button>
        </form>
        <p class="text-center mt-4 text-sm">Belum punya akun? <a href="register.php" class="text-blue-600">Daftar</a>
        </p>
    </div>
</body>

</html>
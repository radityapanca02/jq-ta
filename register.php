<?php include('config/koneksi.php'); ?>

<?php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];

    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        $query = "INSERT INTO user (username, password, nama_lengkap, no_handphone, email)
                  VALUES ('$username', MD5('$password'), '$nama', '$no_hp', '$email')";
        mysqli_query($koneksi, $query);
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Daftar Akun</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full mb-3 p-2 border rounded">
            <input type="password" name="password" placeholder="Password" required
                class="w-full mb-3 p-2 border rounded">
            <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full mb-3 p-2 border rounded">
            <input type="text" name="no_hp" placeholder="No Handphone" class="w-full mb-3 p-2 border rounded">
            <input type="email" name="email" placeholder="Email" class="w-full mb-3 p-2 border rounded">
            <button type="submit" name="register"
                class="bg-blue-500 text-white w-full py-2 rounded hover:bg-blue-600">Daftar</button>
        </form>
        <p class="text-center mt-4 text-sm">Sudah punya akun? <a href="index.php" class="text-blue-600">Login</a></p>
    </div>
</body>

</html>
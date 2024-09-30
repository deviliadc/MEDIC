<?php
session_start(); // Memulai sesi
include 'connect.php'; // Menghubungkan ke database
$error = ''; // Inisialisasi variabel untuk menyimpan pesan kesalahan

// Memeriksa apakah formulir telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengamankan data input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk memeriksa email
    $sql = "SELECT * FROM customer WHERE email='$email'";
    $result = $conn->query($sql);

    // Memeriksa apakah query berhasil
    if (!$result) {
        die("Query gagal: " . $conn->error); // Menampilkan pesan kesalahan jika query gagal
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Mengambil data pengguna

        // Memverifikasi password
        if ($user['password'] == $password) {
            $_SESSION['username'] = $user['username']; // Menyimpan username ke dalam sesi
            echo "<script>
                    alert('Login Berhasil!'); // Menampilkan pesan sukses
                    window.location.href = 'index.php'; // Mengarahkan ke halaman utama
                </script>";
            exit(); // Menghentikan eksekusi skrip setelah redirect
        } else {
            $error = "Email atau password salah."; // Pesan kesalahan jika password salah
            error_log("Verifikasi password gagal untuk email: $email"); // Mencatat kesalahan ke log
        }
    } else {
        $error = "Email atau password salah."; // Pesan kesalahan jika email tidak ditemukan
        error_log("Tidak ada pengguna ditemukan dengan email: $email"); // Mencatat kesalahan ke log
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - MEDIC</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .login-container {
            max-width: 400px; /* Lebar maksimal form */
            margin: auto; /* Tengah */
            padding: 20px; /* Padding di dalam form */
            background-color: #fff; /* Warna latar belakang form */
            border-radius: 8px; /* Sudut melengkung */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Bayangan */
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <a href="index.php">
            <img src="assets/logo-1.png" alt="Logo MEDIC" class="img-fluid mb-4" style="max-width: 150px; display: block; margin: 0 auto;"> <!-- Logo MEDIC -->
        </a>
        <div class="login-container">
            <h2 class="text-center">Login</h2> <!-- Judul form login -->
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"> <!-- Pesan kesalahan jika ada -->
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action=""> <!-- Form untuk login -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required> <!-- Input untuk email -->
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required> <!-- Input untuk password -->
                </div>
                <button type="submit" class="btn btn-primary">Login</button> <!-- Tombol untuk login -->
            </form>
            <p class="mt-3 text-center">Belum punya akun? <a href="register.php">Daftar di sini</a></p> <!-- Tautan untuk mendaftar -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Menyertakan Bootstrap JS -->
</body>
</html>

<?php session_start(); 

// Periksa apakah sesi login pengguna tidak ada
if (!isset($_SESSION['admin'])) {
    // Redirect pengguna ke halaman login
    echo "<script>
            alert('Anda harus login!');
            window.location.href = 'login.php';
            </script>";
    exit; // Pastikan untuk keluar dari skrip setelah melakukan redirect
} else {
    $admin = $_SESSION['admin'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDIC Admin</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <header class="sticky-top bg-light shadow">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php">
                    <img src="../assets/logo-main.png" alt="Logo" style="height:30px; margin-right: 8px;">
                    MEDIC Admin
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto d-flex align-items-center"> <!-- Kelas flexbox untuk menyelaraskan item vertikal -->
                        <li class="nav-item">
                            <a class="btn btn-danger" href="../admin/logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QiT7gnpGVJO7zlZmThBXzBAsJtnszQfjEl4G6UrfMvhSz4rA/S98IbxJgtHlntq+" crossorigin="anonymous"></script>
</body>
</html>

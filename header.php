<?php
// Memulai sesi untuk melacak pengguna yang masuk
// session_start(); 

// Set durasi timeout (10 menit = 600 detik)
$timeout_duration = 600; // Durasi timeout sesi

// Periksa apakah ada aktivitas terakhir
if (isset($_SESSION['last_activity'])) {
    // Hitung selisih waktu antara sekarang dan aktivitas terakhir
    $elapsed_time = time() - $_SESSION['last_activity'];

    // Jika waktu yang berlalu lebih dari 10 menit, logout pengguna
    if ($elapsed_time >= $timeout_duration) {
        session_unset(); // Menghapus semua variabel sesi
        session_destroy(); // Menghancurkan sesi
        header("Location: login.php?timeout=1"); // Redirect ke halaman login dengan parameter timeout
        exit(); // Menghentikan eksekusi skrip setelah redirect
    }
}

// Perbarui waktu aktivitas terakhir
$_SESSION['last_activity'] = time(); // Menyimpan waktu terakhir aktivitas
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDIC</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Menyelaraskan item secara vertikal di navbar */
        .navbar-nav .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%; /* Pastikan item nav mengambil tinggi penuh */
            padding: 10px 15px; /* Sesuaikan padding untuk jarak yang seimbang */
            transition: background-color 0.3s ease-in-out;
        }

        /* Efek hover untuk link navigasi */
        .navbar-nav .nav-link:hover {
            background-color: #e9ecef; /* Warna latar abu-abu muda saat hover */
        }

        /* Menyelaraskan tinggi dan jarak pada link yang mengandung ikon */
        .navbar-nav .nav-link i {
            margin-right: 8px;
        }

        /* Gaya button untuk Login dan Logout */
        .btn-custom {
            margin-top: 5px;
            margin-left: 10px; /* Ruang antara button dan link */
            padding: 10px 20px; /* Sesuaikan padding untuk button */
            border-radius: 15px; /* Sudut membulat */
        }
        
        .guest-link {
            pointer-events: none; /* Menonaktifkan hover untuk link Guest */
            color: inherit; /* Tetap menggunakan warna asli */
        }
        
        /* Flexbox untuk button Logout agar responsif */
        .nav-item.logout-container {
            display: flex;
            justify-content: center; /* Posisi button di tengah secara horizontal */
            align-items: center; /* Posisi button di tengah secara vertikal */
            flex-grow: 1; /* Membuat elemen ini mengembang untuk mengambil ruang yang tersedia */
        }
    </style>
</head>
<body>
    <header class="sticky-top bg-light shadow">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <!-- Navbar Brand (Tengah) -->
                <a class="navbar-brand fw-bold" href="index.php">
                    <img src="assets/logo-main.png" alt="Logo" style="height:30px; margin-right: 8px;">
                    MEDIC
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <!-- Bagian kanan header (Menu Profil/Guest) -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="produk.php">Produk</a>
                        </li>
                        <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php">Keranjang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php">Riwayat</a>
                        </li>
                        <!-- Button Logout dengan Flexbox untuk responsivitas -->
                        <li class="nav-item logout-container">
                            <a class="btn btn-danger btn-custom" href="logout.php">Logout</a>
                        </li>
                        <!-- Menampilkan username pengguna yang login -->
                        <li class="nav-item">
                            <a href="account.php" class="nav-link d-flex align-items-center">
                                <i class="bi bi-person-circle" style="font-size: 1.5rem; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-custom" href="login.php">Login</a>
                        </li>
                        <!-- Menampilkan teks "Guest" saat pengguna belum login -->
                        <li class="nav-item">
                            <span class="nav-link d-flex align-items-center guest-link">
                                <i class="bi bi-person" style="font-size: 1.5rem;"></i>
                                Guest
                            </span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QiT7gnpGVJO7zlZmThBXzBAsJtnszQfjEl4G6UrfMvhSz4rA/S98IbxJgtHlntq+" crossorigin="anonymous"></script>
</body>
</html>

<!-- Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<nav id="sidebar" class="bg-light border-end">
    <div class="position-sticky">
        <!-- Menampilkan profil pengguna -->
        <div class="d-flex align-items-center p-3" style="margin-top: 50px;">
            <i class="bi bi-person-circle" style="font-size: 1.5rem; margin-right: 20px;"></i>
            <span class="fw-bold"><?php echo isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Admin'; ?></span>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../admin/index.php">
                    <i class="bi bi-house-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/kategori.php">
                    <i class="bi bi-tags-fill"></i> Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/produk.php">
                    <i class="bi bi-box-fill"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/penjualan.php">
                    <i class="bi bi-file-earmark-text-fill"></i> Penjualan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/pelanggan.php">
                    <i class="bi bi-person-fill"></i> Pelanggan
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
        z-index: 1000; /* Pastikan sidebar berada di atas konten */
        padding-top: 20px;
        overflow-y: auto; /* Scrollable jika konten sidebar terlalu panjang */
    }

    .nav-item + .nav-item {
        border-top: 1px solid #dee2e6; /* Menambahkan batas antara item menu */
    }

    .nav-link {
        color: black; /* Warna teks */
        padding: 10px 15px;
    }

    .nav-link i {
        margin-right: 10px; /* Jarak antara ikon dan teks */
    }

    .nav-link:hover {
        background-color: #e9ecef; /* Warna latar belakang saat hover */
    }

    .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
        #sidebar {
            width: 100%; /* Sidebar menjadi penuh pada layar kecil */
            height: auto; /* Mengatur tinggi otomatis */
            position: relative; /* Mengatur agar sidebar berada di dalam konten, bukan fixed */
        }
    }
</style>

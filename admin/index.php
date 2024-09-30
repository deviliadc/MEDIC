<?php
include '../connect.php';
include 'header.php'; 

// Periksa apakah sesi login pengguna tidak ada
if (!isset($_SESSION['admin'])) {
    // Redirect pengguna ke halaman login
    echo "<script>
            alert('Anda harus login!');
            window.location.href = 'login.php';
            </script>";
    exit; // Pastikan untuk keluar dari skrip setelah melakukan redirect
} else {
    $admin = $_SESSION['admin']; // Mengambil informasi admin dari sesi
}

// Ambil total produk
$sqlTotalProduk = "SELECT COUNT(*) AS total_produk FROM produk";
$resultTotalProduk = $conn->query($sqlTotalProduk); // Eksekusi query untuk menghitung total produk
$totalProduk = $resultTotalProduk->fetch_assoc()['total_produk']; // Ambil total produk dari hasil query

// Ambil total kategori
$sqlTotalKategori = "SELECT COUNT(*) AS total_kategori FROM kategori";
$resultTotalKategori = $conn->query($sqlTotalKategori); // Eksekusi query untuk menghitung total kategori
$totalKategori = $resultTotalKategori->fetch_assoc()['total_kategori']; // Ambil total kategori dari hasil query

// Ambil total customer
$sqlTotalCustomer = "SELECT COUNT(*) AS total_customer FROM customer";
$resultTotalCustomer = $conn->query($sqlTotalCustomer); // Eksekusi query untuk menghitung total customer
$totalCustomer = $resultTotalCustomer->fetch_assoc()['total_customer']; // Ambil total customer dari hasil query

// Ambil total pembelian
$sqlTotalPembelian = "SELECT COUNT(*) AS total_pembelian FROM pembelian";
$resultTotalPembelian = $conn->query($sqlTotalPembelian); // Eksekusi query untuk menghitung total pembelian
$totalPembelian = $resultTotalPembelian->fetch_assoc()['total_pembelian']; // Ambil total pembelian dari hasil query

// Ambil total pendapatan
$sqlTotalPendapatan = "SELECT SUM(total_pembelian) AS total_pendapatan FROM pembelian";
$resultTotalPendapatan = $conn->query($sqlTotalPendapatan); // Eksekusi query untuk menghitung total pendapatan
$totalPendapatan = $resultTotalPendapatan->fetch_assoc()['total_pendapatan']; // Ambil total pendapatan dari hasil query

// Ambil produk terlaris hanya jika ada pembelian
$sqlProdukTerlaris = "SELECT p.nama_produk, SUM(pb.quantity) AS total_terjual 
                        FROM detail_pembelian pb 
                        JOIN produk p ON pb.id_produk = p.id_produk 
                        JOIN pembelian b ON pb.id_pembelian = b.id_pembelian 
                        GROUP BY pb.id_produk 
                        HAVING total_terjual > 0 AND COUNT(b.id_pembelian) > 0 
                        ORDER BY total_terjual DESC 
                        LIMIT 5"; // Query untuk mengambil 5 produk terlaris berdasarkan jumlah terjual
$resultProdukTerlaris = $conn->query($sqlProdukTerlaris); // Eksekusi query untuk mendapatkan produk terlaris

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS untuk background header tabel */
        .table thead th {
            background-color: gray; /* Warna latar belakang */
            color: white; /* Warna teks */
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto">
            <?php include 'sidebar.php'; ?> <!-- Menyertakan sidebar -->
        </div>
        <div class="col" style="margin-left: 250px;"> <!-- Sesuaikan margin agar tidak bertabrakan dengan sidebar -->
        <div class="container mt-5">
            <h2 class="text-center">Dashboard</h2>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk</h5>
                            <p class="card-text"><?php echo $totalProduk; ?></p> <!-- Menampilkan total produk -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Kategori</h5>
                            <p class="card-text"><?php echo $totalKategori; ?></p> <!-- Menampilkan total kategori -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Customer</h5>
                            <p class="card-text"><?php echo $totalCustomer; ?></p> <!-- Menampilkan total customer -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Pembelian</h5>
                            <p class="card-text"><?php echo $totalPembelian; ?></p> <!-- Menampilkan total pembelian -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Pendapatan</h5>
                            <p class="card-text">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></p> <!-- Menampilkan total pendapatan dengan format rupiah -->
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-5">Produk Terlaris</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultProdukTerlaris->num_rows > 0): ?> <!-- Memeriksa apakah ada produk terlaris -->
                        <?php while ($row = $resultProdukTerlaris->fetch_assoc()): ?> <!-- Mengambil setiap produk terlaris -->
                            <tr>
                                <td><?php echo $row['nama_produk']; ?></td> <!-- Menampilkan nama produk -->
                                <td><?php echo $row['total_terjual']; ?></td> <!-- Menampilkan total terjual -->
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">Tidak ada produk terlaris.</td> <!-- Menampilkan pesan jika tidak ada produk terlaris -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?> <!-- Menutup koneksi database -->

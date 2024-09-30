<?php 
// Menghubungkan ke database
include '../connect.php';

// Ambil data kategori dan jumlah produk
$sql = "SELECT k.id_kategori, k.nama_kategori, COUNT(p.id_produk) AS total_produk
        FROM kategori k
        LEFT JOIN produk p ON k.id_kategori = p.id_kategori
        GROUP BY k.id_kategori";
$result = $conn->query($sql); // Menjalankan query untuk mendapatkan data kategori

// Menyertakan header halaman
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk</title>
    <!-- Mengimpor CSS Bootstrap -->
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
            <?php include 'sidebar.php'; // Menyertakan sidebar ?>
        </div>
        <div class="col" style="margin-left: 250px;"> <!-- Sesuaikan margin agar tidak bertabrakan dengan sidebar -->
            <div class="container mt-5">
                <h2 class="text-center">Kategori</h2>
                <!-- Tombol untuk menambah kategori baru -->
                <a href="tambah_kategori.php" class="btn btn-primary mb-3">Tambah Kategori</a>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Kategori</th>
                            <th>Nama Kategori</th>
                            <th>Total Produk</th>
                            <th>Aksi</th> <!-- Kolom untuk aksi Edit dan Hapus -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): // Loop melalui setiap kategori ?>
                            <tr>
                                <td><?php echo $row['id_kategori']; ?></td> <!-- Menampilkan ID Kategori -->
                                <td><?php echo $row['nama_kategori']; ?></td> <!-- Menampilkan Nama Kategori -->
                                <td><?php echo $row['total_produk']; ?></td> <!-- Menampilkan Total Produk dalam kategori -->
                                <td>
                                    <!-- Tombol Edit untuk mengubah kategori -->
                                    <a href="edit_kategori.php?id_kategori=<?php echo $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <!-- Tombol Hapus untuk menghapus kategori dengan konfirmasi -->
                                    <a href="hapus_kategori.php?id_kategori=<?php echo $row['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; // Akhir loop ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Mengimpor JavaScript Bootstrap -->
</body>
</html>

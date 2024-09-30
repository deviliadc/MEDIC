<?php 
// Menghubungkan ke file connect.php untuk koneksi ke database
include '../connect.php';

// Mengambil data produk dan kategori dari database menggunakan JOIN
$sql = "SELECT p.id_produk, p.nama_produk, p.harga_produk, p.berat_produk, p.foto_produk, p.deskripsi_produk, k.nama_kategori 
        FROM produk p 
        JOIN kategori k ON p.id_kategori = k.id_kategori
        ORDER BY p.id_produk"; // Mengurutkan data produk berdasarkan id_produk
$result = $conn->query($sql); // Menjalankan query dan menyimpan hasilnya

// Menghubungkan ke file header.php untuk menampilkan header halaman
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <!-- Menghubungkan ke Bootstrap 5 untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS untuk background header tabel */
        .table thead th {
            background-color: gray; /* Warna latar belakang header tabel */
            color: white; /* Warna teks header tabel */
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto">
            <!-- Menyertakan sidebar dari file sidebar.php -->
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col" style="margin-left: 250px;">
            <div class="container mt-5">
                <h2 class="text-center">Daftar Produk</h2>
                <div class="mb-3">
                    <!-- Tombol untuk menambahkan produk baru -->
                    <a href="tambah_produk.php" class="btn btn-primary">Tambah Produk</a>
                </div>
                <!-- Tabel untuk menampilkan daftar produk -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Harga Produk</th>
                            <th>Berat Produk (gram)</th>
                            <th>Kategori</th>
                            <th>Foto</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Cek apakah ada data produk yang diambil dari database -->
                        <?php if ($result->num_rows > 0): ?>
                            <!-- Jika ada data produk, tampilkan di dalam tabel -->
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_produk']; ?></td>
                                    <td><?php echo $row['nama_produk']; ?></td>
                                    <td><?php echo number_format($row['harga_produk'], 2); // Format angka untuk harga ?></td>
                                    <td><?php echo $row['berat_produk']; ?></td>
                                    <td><?php echo $row['nama_kategori']; ?></td>
                                    <td><img src="../assets/foto_produk/<?php echo $row['foto_produk']; ?>" alt="<?php echo $row['nama_produk']; ?>" style="max-width: 100px;"></td>
                                    <td><?php echo $row['deskripsi_produk']; ?></td>
                                    <td>
                                        <!-- Tombol untuk edit dan hapus produk -->
                                        <a href="edit_produk.php?id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="hapus_produk.php?id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Jika tidak ada data produk, tampilkan pesan kosong -->
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada produk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Menghubungkan ke Bootstrap JS untuk interaktivitas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- Menutup koneksi database -->
<?php $conn->close(); ?> 

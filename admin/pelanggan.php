<?php 
include '../connect.php'; // Menghubungkan ke database

// Ambil data pelanggan dari database
$sql = "SELECT c.id_customer, c.username, c.email, c.dob, g.nama_gender, c.address, c.contact
        FROM customer c
        LEFT JOIN gender g ON c.id_gender = g.id_gender";
$result = $conn->query($sql); // Jalankan query dan simpan hasilnya

include 'header.php'; // Memasukkan file header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS untuk mengatur background header tabel */
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
            <?php include 'sidebar.php'; // Memasukkan file sidebar ?>
        </div>
        <div class="col" style="margin-left: 250px;">
            <div class="container mt-5">
                <h2 class="text-center">Daftar Pelanggan</h2>
                
                <!-- Tombol untuk menambah pelanggan baru -->
                <a href="tambah_pelanggan.php" class="btn btn-primary mb-3">Tambah Pelanggan</a>
                
                <!-- Tabel untuk menampilkan data pelanggan -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pelanggan</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Tanggal Lahir</th>
                            <th>Gender</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Memeriksa apakah ada hasil dari query -->
                        <?php if ($result->num_rows > 0): ?> 
                            <!-- Looping untuk menampilkan setiap baris data pelanggan -->
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_customer']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['dob']; ?></td>
                                    <td><?php echo $row['nama_gender']; ?></td>
                                    <td><?php echo $row['address']; ?></td>
                                    <td><?php echo $row['contact']; ?></td>
                                    <td>
                                        <!-- Tombol untuk mengedit data pelanggan -->
                                        <a href="edit_pelanggan.php?id_customer=<?php echo $row['id_customer']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        
                                        <!-- Tombol untuk menghapus data pelanggan dengan konfirmasi -->
                                        <a href="hapus_pelanggan.php?id_customer=<?php echo $row['id_customer']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Jika tidak ada data, tampilkan pesan ini -->
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Bootstrap untuk interaksi yang lebih baik -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
session_start(); // Memulai sesi untuk menyimpan data sementara pengguna
include '../connect.php'; // Menghubungkan ke database

$alertMessage = ''; // Variabel untuk menyimpan pesan alert
$alertType = ''; // Variabel untuk menyimpan tipe alert (success/danger)

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Mengecek jika form dikirim menggunakan metode POST
    $nama_kategori = $conn->real_escape_string($_POST['nama_kategori']); // Mengamankan input dari user

    // Cek apakah kategori dengan nama yang sama sudah ada di database
    $checkSql = "SELECT * FROM kategori WHERE nama_kategori='$nama_kategori'";
    $checkResult = $conn->query($checkSql); // Mengeksekusi query untuk pengecekan

    if ($checkResult->num_rows > 0) { // Jika kategori sudah ada, tampilkan pesan error
        $alertMessage = "Kategori sudah ada!";
        $alertType = "danger";
    } else {
        // Jika kategori belum ada, lakukan penyisipan data ke database
        $sql = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
        
        if ($conn->query($sql) === TRUE) { // Jika query berhasil, tampilkan pesan sukses
            $alertMessage = "Kategori berhasil ditambahkan!";
            $alertType = "success";
        } else { // Jika query gagal, tampilkan pesan kesalahan
            $alertMessage = "Terjadi kesalahan: " . $conn->error;
            $alertType = "danger";
        }
    }
}

$conn->close(); // Menutup koneksi ke database setelah selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Mengatur karakter yang digunakan -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif untuk perangkat mobile -->
    <title>Tambah Kategori</title> <!-- Judul halaman -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Menghubungkan dengan Bootstrap untuk styling -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Tambah Kategori</h2>
            <!-- Tombol close di kanan atas yang mengarahkan kembali ke halaman kategori -->
            <a href="kategori.php" class="btn-close" aria-label="Close"></a>
        </div>
        <?php if ($alertMessage): ?> <!-- Menampilkan alert jika ada pesan -->
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?> <!-- Menampilkan pesan alert -->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!-- Tombol untuk menutup alert -->
            </div>
        <?php endif; ?>
        
        <!-- Form untuk menambahkan kategori -->
        <form method="POST" action="tambah_kategori.php">
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori</label> <!-- Label untuk input kategori -->
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required> <!-- Input untuk nama kategori -->
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button> <!-- Tombol submit untuk menyimpan data -->
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Menghubungkan dengan Bootstrap JS untuk interaktivitas -->
</body>
</html>

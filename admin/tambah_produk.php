<?php 
session_start(); // Memulai sesi untuk menjaga data sesi pengguna
include '../connect.php'; // Menghubungkan ke file koneksi database
include '../file_uploader.php'; // Menghubungkan ke kelas FileUploader

// Inisialisasi variabel untuk menyimpan pesan alert jika ada kesalahan atau sukses
$alertMessage = ''; 
$alertType = ''; 

// Inisialisasi FileUploader
$fileUploader = new FileUploader("../assets/foto_produk/");

// Mengecek apakah form dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil dan mengamankan data input dari form
    $nama_produk = $conn->real_escape_string($_POST['nama_produk']);
    $harga_produk = $_POST['harga_produk'];
    $berat_produk = $_POST['berat_produk'];
    $deskripsi_produk = $conn->real_escape_string($_POST['deskripsi_produk']);
    $id_kategori = $_POST['id_kategori'];

    // Mengupload foto produk
    $fileUploadResult = $fileUploader->upload($_FILES['foto_produk']); // Panggil metode upload

    if ($fileUploadResult) {
        // Jika upload berhasil, masukkan data produk ke database
        $sql = "INSERT INTO produk (nama_produk, harga_produk, berat_produk, foto_produk, deskripsi_produk, id_kategori) 
                VALUES ('$nama_produk', '$harga_produk', '$berat_produk', '$fileUploadResult', '$deskripsi_produk', '$id_kategori')";

        // Jika query berhasil dijalankan
        if ($conn->query($sql) === TRUE) {
            $alertMessage = "Produk berhasil ditambahkan!"; // Pesan sukses
            $alertType = "success";  // Tipe alert untuk sukses
        } else {
            $alertMessage = "Terjadi kesalahan saat menyimpan data: " . $conn->error; // Pesan error
            $alertType = "danger";  // Tipe alert untuk error
        }
    } else {
        // Jika upload gagal
        $alertMessage = "Maaf, terjadi kesalahan saat mengupload gambar."; 
        $alertType = "danger";  // Tipe alert untuk error upload
    }
}

$conn->close(); // Menutup koneksi database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Tambah Produk</h2>
            <!-- Tombol close di kanan atas untuk kembali ke halaman produk -->
            <a href="produk.php" class="btn-close" aria-label="Close"></a>
        </div>

        <!-- Menampilkan pesan alert jika ada -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form untuk menambahkan produk -->
        <form method="POST" action="tambah_produk.php" enctype="multipart/form-data">
            <!-- Input untuk nama produk -->
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <!-- Input untuk harga produk -->
            <div class="mb-3">
                <label for="harga_produk" class="form-label">Harga Produk</label>
                <input type="number" step="0.01" class="form-control" id="harga_produk" name="harga_produk" required>
            </div>
            <!-- Input untuk berat produk -->
            <div class="mb-3">
                <label for="berat_produk" class="form-label">Berat Produk (gram)</label>
                <input type="double" class="form-control" id="berat_produk" name="berat_produk" required>
            </div>
            <!-- Input untuk upload foto produk -->
            <div class="mb-3">
                <label for="foto_produk" class="form-label">Foto Produk</label>
                <input type="file" class="form-control" id="foto_produk" name="foto_produk" accept="image/*" required>
            </div>
            <!-- Input untuk deskripsi produk -->
            <div class="mb-3">
                <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" required></textarea>
            </div>
            <!-- Dropdown untuk memilih kategori produk -->
            <div class="mb-3">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select class="form-select" id="id_kategori" name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <!-- Tambahkan opsi kategori dari database -->
                    <?php
                    include '../connect.php';  // Koneksi ke database
                    $sql = "SELECT id_kategori, nama_kategori FROM kategori"; // Query untuk mengambil data kategori
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id_kategori'] . '">' . $row['nama_kategori'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <!-- Tombol untuk submit form -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

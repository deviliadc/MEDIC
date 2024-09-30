<?php 
session_start(); // Memulai session agar dapat menyimpan informasi pengguna yang login
include '../connect.php'; // Menghubungkan ke database

$alertMessage = ''; // Variabel untuk menyimpan pesan alert
$alertType = ''; // Variabel untuk menyimpan tipe alert (success/danger)

// Ambil ID kategori dari URL
if (isset($_GET['id_kategori'])) { // Mengecek apakah ID kategori tersedia di URL
    $id_kategori = $_GET['id_kategori']; // Menyimpan ID kategori dari URL

    // Ambil data kategori dari database berdasarkan ID kategori
    $sql = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori'"; 
    $result = $conn->query($sql); // Menjalankan query
    $kategori = $result->fetch_assoc(); // Mengambil data kategori sebagai array

    if ($_SERVER["REQUEST_METHOD"] == "POST") { // Mengecek apakah form dikirim dengan metode POST
        // Mengamankan input dari user agar terhindar dari SQL Injection
        $new_id_kategori = $conn->real_escape_string($_POST['id_kategori']);
        $nama_kategori = $conn->real_escape_string($_POST['nama_kategori']);

        // Update kategori dengan ID baru dan nama kategori baru
        $sql = "UPDATE kategori SET id_kategori='$new_id_kategori', nama_kategori='$nama_kategori' WHERE id_kategori = '$id_kategori'";
        
        // Mengecek apakah update berhasil
        if ($conn->query($sql) === TRUE) { 
            $alertMessage = "Kategori berhasil diubah!"; // Pesan jika berhasil
            $alertType = "success"; // Tipe alert sukses
            header("Location: kategori.php"); // Redirect ke halaman kategori setelah update
            exit(); // Menghentikan eksekusi kode setelah redirect
        } else {
            $alertMessage = "Terjadi kesalahan: " . $conn->error; // Pesan jika terjadi kesalahan
            $alertType = "danger"; // Tipe alert bahaya
        }
    }
} else {
    // Jika ID kategori tidak tersedia di URL, redirect ke halaman kategori
    header("Location: kategori.php"); 
}

$conn->close(); // Menutup koneksi database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Edit Kategori</h2>
            <!-- Tombol close untuk kembali ke halaman kategori -->
            <a href="kategori.php" class="btn-close" aria-label="Close"></a>
        </div>

        <!-- Menampilkan pesan alert jika ada -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; // Menampilkan pesan alert ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Form untuk mengedit kategori -->
        <form method="POST" action="">
            <div class="mb-3">
                <!-- Input untuk ID kategori -->
                <label for="id_kategori" class="form-label">ID Kategori</label>
                <input type="text" class="form-control" id="id_kategori" name="id_kategori" value="<?php echo htmlspecialchars($kategori['id_kategori']); ?>" required>
            </div>
            <div class="mb-3">
                <!-- Input untuk nama kategori -->
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
            </div>
            <!-- Tombol untuk menyimpan perubahan -->
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
    <!-- Menyertakan Bootstrap JS untuk mendukung elemen interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

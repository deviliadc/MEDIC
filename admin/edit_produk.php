<?php
session_start();
include '../connect.php';

// Inisialisasi variabel untuk pesan kesalahan atau sukses
$alertMessage = '';
$alertType = '';

// Ambil ID produk dari URL
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Ambil data produk dari database
    $sql = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result = $conn->query($sql);
    $produk = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nama_produk = $conn->real_escape_string($_POST['nama_produk']);
        $harga_produk = $_POST['harga_produk'];
        $berat_produk = $_POST['berat_produk'];
        $deskripsi_produk = $conn->real_escape_string($_POST['deskripsi_produk']);
        $id_kategori = $_POST['id_kategori'];

        // Mengupdate foto produk jika ada file yang diupload
        if ($_FILES['foto_produk']['name']) {
            $namaFile = $_FILES['foto_produk']['name'];  // Ambil nama asli file
            $lokasi = $_FILES['foto_produk']['tmp_name'];  // Lokasi sementara file
            $target_dir = "../assets/foto_produk/";  // Direktori tujuan penyimpanan
            $target_file = $target_dir . basename($namaFile);  // Path lengkap untuk menyimpan file

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);  // Buat folder jika belum ada
            }

            if (move_uploaded_file($lokasi, $target_file)) {
                $foto_produk = $namaFile;  // Simpan nama file saja
            } else {
                $alertMessage = "Maaf, terjadi kesalahan saat mengupload gambar.";
                $alertType = "danger";
            }
        } else {
            // Jika tidak ada foto baru, gunakan foto lama
            $foto_produk = $produk['foto_produk'];
        }

        // Update data produk di database
        $sql = "UPDATE produk SET nama_produk='$nama_produk', harga_produk='$harga_produk', berat_produk='$berat_produk',
                foto_produk='$foto_produk', deskripsi_produk='$deskripsi_produk', id_kategori='$id_kategori' 
                WHERE id_produk = '$id_produk'";

        if ($conn->query($sql) === TRUE) {
            // Setelah berhasil update, arahkan ke halaman produk
            header("Location: produk.php?msg=update_success");
            exit;  // Hentikan script setelah redirect
        } else {
            $alertMessage = "Terjadi kesalahan saat menyimpan data: " . $conn->error;
            $alertType = "danger";
        }
    }
} else {
    header("Location: produk.php");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Ubah Produk</h2>
            <a href="produk.php" class="btn-close" aria-label="Close"></a>
        </div>

        <!-- Tampilkan pesan alert jika ada -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga_produk" class="form-label">Harga Produk</label>
                <input type="number" step="0.01" class="form-control" id="harga_produk" name="harga_produk" value="<?php echo $produk['harga_produk']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="berat_produk" class="form-label">Berat Produk (gram)</label>
                <input type="double" class="form-control" id="berat_produk" name="berat_produk" value="<?php echo $produk['berat_produk']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="foto_produk" class="form-label">Foto Produk</label>
                <input type="file" class="form-control" id="foto_produk" name="foto_produk" accept="image/*">
                <?php if ($produk['foto_produk']): ?>
                    <img src="../assets/foto_produk/<?php echo $produk['foto_produk']; ?>" alt="Foto Produk" class="img-thumbnail mt-3" width="150">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" required><?php echo $produk['deskripsi_produk']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select class="form-select" id="id_kategori" name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php
                    include '../connect.php';
                    $sql = "SELECT id_kategori, nama_kategori FROM kategori";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id_kategori'] . '"' . ($produk['id_kategori'] == $row['id_kategori'] ? ' selected' : '') . '>' . $row['nama_kategori'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

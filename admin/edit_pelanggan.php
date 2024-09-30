<?php 
session_start(); // Memulai sesi untuk mengelola data pengguna
include '../connect.php'; // Menghubungkan ke database

// Ambil data pelanggan berdasarkan ID yang diterima dari URL
$id_customer = $_GET['id_customer'];
$sql = "SELECT * FROM customer WHERE id_customer = '$id_customer'"; // Query untuk mendapatkan data pelanggan
$result = $conn->query($sql); // Menjalankan query
$customer = $result->fetch_assoc(); // Mengambil data pelanggan dalam bentuk associative array

// Inisialisasi pesan alert untuk menampilkan feedback kepada pengguna
$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Memeriksa apakah form telah disubmit
    // Mengambil dan membersihkan data dari form
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $_POST['dob']; // Mengambil tanggal lahir
    $id_gender = $_POST['id_gender']; // Mengambil ID gender
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);

    // Query untuk memperbarui data pelanggan
    $sql = "UPDATE customer SET username='$username', email='$email', dob='$dob', id_gender='$id_gender', address='$address', contact='$contact' 
            WHERE id_customer='$id_customer'";

    // Menjalankan query dan memeriksa hasilnya
    if ($conn->query($sql) === TRUE) {
        $alertMessage = 'Pelanggan berhasil diubah!'; // Pesan sukses
        $alertType = 'success'; // Tipe alert sukses
    } else {
        $alertMessage = 'Terjadi kesalahan: ' . $conn->error; // Pesan kesalahan
        $alertType = 'danger'; // Tipe alert bahaya
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Mengimpor CSS Bootstrap -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Edit Pelanggan</h2>
            <!-- Tombol untuk menutup halaman ini dan kembali ke daftar pelanggan -->
            <a href="pelanggan.php" class="btn-close" aria-label="Close"></a>
        </div>

        <!-- Tampilkan pesan alert jika ada -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; // Menampilkan pesan alert ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!-- Tombol untuk menutup alert -->
            </div>
        <?php endif; ?>

        <!-- Form untuk mengedit data pelanggan -->
        <form method="POST" action="edit_pelanggan.php?id_customer=<?php echo $id_customer; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $customer['username']; ?>" required> <!-- Input untuk username -->
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer['email']; ?>" required> <!-- Input untuk email -->
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $customer['dob']; ?>" required> <!-- Input untuk tanggal lahir -->
            </div>
            <div class="mb-3">
                <label for="id_gender" class="form-label">Gender</label>
                <select class="form-select" id="id_gender" name="id_gender" required> <!-- Dropdown untuk memilih gender -->
                    <?php
                    // Query untuk mendapatkan data gender dari database
                    $genderSql = "SELECT id_gender, nama_gender FROM gender";
                    $genderResult = $conn->query($genderSql);
                    while ($row = $genderResult->fetch_assoc()) {
                        // Menentukan opsi yang dipilih berdasarkan gender pelanggan saat ini
                        $selected = ($row['id_gender'] == $customer['id_gender']) ? 'selected' : '';
                        echo '<option value="' . $row['id_gender'] . '" ' . $selected . '>' . $row['nama_gender'] . '</option>'; // Menampilkan opsi gender
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" required><?php echo $customer['address']; ?></textarea> <!-- Input untuk alamat -->
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Kontak</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $customer['contact']; ?>" required> <!-- Input untuk kontak -->
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button> <!-- Tombol untuk menyimpan perubahan -->
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Mengimpor JavaScript Bootstrap -->
</body>
</html>

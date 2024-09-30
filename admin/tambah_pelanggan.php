<?php 
session_start(); // Memulai sesi untuk menyimpan data sementara pengguna
include '../connect.php'; // Menghubungkan ke database

$genders = $conn->query("SELECT id_gender, nama_gender FROM gender"); // Mengambil data gender dari tabel gender

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Mengecek jika form dikirim menggunakan metode POST
    $username = $conn->real_escape_string($_POST['username']); // Mengamankan input dari user
    $password = $conn->real_escape_string($_POST['password']); // Mengamankan input dari user
    $email = $conn->real_escape_string($_POST['email']); // Mengamankan input dari user
    $dob = $conn->real_escape_string($_POST['dob']); // Mengamankan input dari user
    $gender = $conn->real_escape_string($_POST['gender']); // Mengamankan input dari user
    $address = $conn->real_escape_string($_POST['address']); // Mengamankan input dari user
    $contact = $conn->real_escape_string($_POST['contact']); // Mengamankan input dari user

    // Menyimpan data pelanggan ke dalam database
    $sql = "INSERT INTO customer (username, password, email, dob, id_gender, address, contact) 
    VALUES ('$username', '$password', '$email', '$dob', '$gender', '$address', '$contact')";

    if ($conn->query($sql) === TRUE) { // Jika query berhasil, redirect ke halaman pelanggan
        header("Location: pelanggan.php"); // Redirect ke halaman pelanggan
        exit();
    } else { // Jika query gagal, tampilkan pesan kesalahan
        echo "Terjadi kesalahan: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Mengatur karakter yang digunakan -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mengatur tampilan agar responsif pada perangkat mobile -->
    <title>Tambah Pelanggan</title> <!-- Judul halaman -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Menghubungkan dengan Bootstrap CSS untuk styling -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>Tambah Pelanggan</h2>
            <!-- Tombol close di kanan atas yang mengarahkan kembali ke halaman pelanggan -->
            <a href="pelanggan.php" class="btn-close" aria-label="Close"></a>
        </div>
        <form method="POST" action="tambah_pelanggan.php"> <!-- Formulir untuk menambahkan pelanggan -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label> <!-- Label untuk input username -->
                <input type="text" class="form-control" id="username" name="username" required> <!-- Input untuk username -->
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label> <!-- Label untuk input password -->
                <input type="password" class="form-control" id="password" name="password" required> <!-- Input untuk password -->
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label> <!-- Label untuk input email -->
                <input type="email" class="form-control" id="email" name="email" required> <!-- Input untuk email -->
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Tanggal Lahir</label> <!-- Label untuk input tanggal lahir -->
                <input type="date" class="form-control" id="dob" name="dob" required> <!-- Input untuk tanggal lahir -->
            </div>
            <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label><br> <!-- Label untuk input gender -->
                    <?php while ($row = $genders->fetch_assoc()): ?> <!-- Loop untuk menampilkan gender sebagai radio button -->
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender<?php echo $row['id_gender']; ?>" value="<?php echo $row['id_gender']; ?>" required> <!-- Radio button untuk pilihan gender -->
                            <label class="form-check-label" for="gender<?php echo $row['id_gender']; ?>"><?php echo $row['nama_gender']; ?></label> <!-- Label untuk pilihan gender -->
                        </div>
                    <?php endwhile; ?>
                </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label> <!-- Label untuk input alamat -->
                <input type="text" class="form-control" id="address" name="address" required> <!-- Input untuk alamat -->
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Kontak</label> <!-- Label untuk input kontak -->
                <input type="text" class="form-control" id="contact" name="contact" required> <!-- Input untuk kontak -->
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button> <!-- Tombol untuk submit formulir -->
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Menghubungkan dengan Bootstrap JS untuk interaktivitas -->
</body>
</html>

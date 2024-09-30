<?php 
session_start(); // Memulai sesi untuk menyimpan data sementara
include 'connect.php'; // Menghubungkan ke database

// Ambil data gender dari database
$genders = $conn->query("SELECT id_gender, nama_gender FROM gender");

// Proses registrasi ketika form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil inputan user dan mengamankannya dengan real_escape_string
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $retype_password = $conn->real_escape_string($_POST['retype_password']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);

    // Validasi kesamaan password dan retype password
    if ($password !== $retype_password) {
        $error = "Password dan retype password tidak cocok."; // Jika tidak cocok, tampilkan pesan error
    } else {
        // Query untuk menyimpan data user ke dalam tabel customer
        $sql = "INSERT INTO customer (username, password, email, dob, id_gender, address, contact) 
                VALUES ('$username', '$password', '$email', '$dob', '$gender', '$address', '$contact')";

        // Jika query berhasil, redirect ke halaman login
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php"); // Redirect ke halaman login
            exit(); // Pastikan script berhenti setelah redirect
        } else {
            // Jika ada error saat menyimpan ke database, tampilkan pesan error
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
    $conn->close(); // Tutup koneksi database setelah proses selesai
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pelanggan - MEDIC</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .register-container {
            max-width: 500px; /* Lebar maksimal form */
            margin: auto; /* Tengah */
            padding: 20px; /* Padding di dalam form */
            background-color: #fff; /* Warna latar belakang form */
            border-radius: 8px; /* Sudut melengkung */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Bayangan */
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <a href="index.php">
            <img src="assets/logo-1.png" alt="Logo MEDIC" class="img-fluid mb-3" style="max-width: 150px; display: block; margin: 0 auto;">
        </a>
        <div class="register-container">
            <h2 class="text-center">Registrasi Pelanggan</h2>
            <!-- Tampilkan pesan error jika ada -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="retype_password" class="form-label">Retype Password</label>
                    <input type="password" class="form-control" id="retype_password" name="retype_password" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="youremail@email.com" required>
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label><br>
                    <!-- Menampilkan pilihan gender yang diambil dari database -->
                    <?php while ($row = $genders->fetch_assoc()): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender_<?php echo $row['id_gender']; ?>" value="<?php echo $row['id_gender']; ?>" required>
                            <label class="form-check-label" for="gender_<?php echo $row['id_gender']; ?>"><?php echo $row['nama_gender']; ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Kontak</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QiT7gnpGVJO7zlZmThBXzBAsJtnszQfjEl4G6UrfMvhSz4rA/S98IbxJgtHlntq+" crossorigin="anonymous"></script>
</body>
</html>

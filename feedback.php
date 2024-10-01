<?php
// Memulai sesi
session_start();
include 'connect.php'; // Memastikan koneksi database di sini

// Mengambil data pengguna dari database
$username = $_SESSION['username']; // Mengambil username dari sesi
$user_query = "SELECT * FROM customer WHERE username='$username'"; // Kuery untuk mendapatkan data pengguna
$user_result = $conn->query($user_query); // Menjalankan kuery
if (!$user_result) { // Memeriksa apakah kuery gagal
    die("Query failed: " . $conn->error); // Menangani kesalahan kuery
}
if ($user_result->num_rows > 0) { // Memeriksa apakah data pengguna ditemukan
    $user = $user_result->fetch_assoc(); // Mengambil data pengguna sebagai array asosiatif
    $id_customer = $user['id_customer']; // Mengambil ID pelanggan
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan feedback telah diisi sebelum memprosesnya
    if (isset($_POST['feedback'])) {
        // Mengambil inputan user dan mengamankannya dengan real_escape_string
        $feedback = $conn->real_escape_string($_POST['feedback']); 

        // Query untuk menyimpan feedback ke dalam tabel feedback
        $sql = "INSERT INTO kontak (id_customer, feedback) 
                VALUES ('$id_customer', '$feedback')";

        // Jika query berhasil, tampilkan pesan sukses
        if ($conn->query($sql) === TRUE) {
            $update_message = "Terima kasih atas feedback Anda!";
        } else {
            // Jika ada error saat menyimpan ke database, tampilkan pesan error
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    } else {
        $error = "Feedback tidak boleh kosong.";
    }
    $conn->close(); // Tutup koneksi database setelah proses selesai
}


include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - MEDIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Feedback</h2>
        <?php if (isset($update_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($update_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nama</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required disabled>
            </div>
            <div class="mb-3">
                <label for="feedback" class="form-label">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

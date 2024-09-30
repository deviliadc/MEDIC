<?php
// Memulai sesi
session_start();
include 'connect.php'; // Memastikan koneksi database di sini

// Mengambil data pengguna dari database
$username = $_SESSION['username']; // Mengambil username dari sesi
$user_query = "SELECT * FROM customer WHERE username = ?"; // Kuery untuk mendapatkan data pengguna
$stmt = $conn->prepare($user_query); // Menggunakan prepared statement untuk keamanan
$stmt->bind_param('s', $username); // 's' untuk tipe data string
$stmt->execute();
$user_result = $stmt->get_result(); // Menjalankan kuery

if ($user_result->num_rows > 0) { // Memeriksa apakah data pengguna ditemukan
    $user = $user_result->fetch_assoc(); // Mengambil data pengguna sebagai array asosiatif
    $id_customer = $user['id_customer']; // Mengambil ID pelanggan
} else {
    echo "User not found.";
    exit();
}

// Proses pembaruan data pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // Anda masih bisa mengambil username jika ingin
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_message = "Format email tidak valid.";
    } else {
        // Update data pengguna dalam database menggunakan prepared statement
        $stmt = $conn->prepare("UPDATE customer SET username = ?, email = ?, dob = ?, address = ?, contact = ? WHERE id_customer = ?");
        $stmt->bind_param('sssssi', $username, $email, $dob, $address, $contact, $id_customer);
    
        if ($stmt->execute()) {
            $update_message = "Data berhasil diperbarui.";
            // Ambil kembali data pengguna yang diperbarui
            $stmt = $conn->prepare("SELECT * FROM customer WHERE id_customer = ?");
            $stmt->bind_param('i', $id_customer);
            $stmt->execute();
            $user_result = $stmt->get_result();
            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc(); // Ambil data pengguna yang diperbarui
            }
        } else {
            $update_message = "Error: " . $conn->error;
        }
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - MEDIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Informasi Akun</h2>
        <?php if (isset($update_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($update_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Kontak</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

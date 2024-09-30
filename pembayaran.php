<?php
session_start(); // Memulai sesi untuk menyimpan pesan alert
include 'connect.php'; // Menyertakan file koneksi ke database
include 'file_uploader.php'; // Menyertakan file uploader

// Ambil ID pembelian dari query parameter
$id_pembelian = isset($_GET['id_pembelian']) ? $_GET['id_pembelian'] : null;

// Pastikan ID pembelian ada
if ($id_pembelian === null) {
    echo "<p>ID pembelian tidak ditemukan.</p>"; // Pesan jika ID tidak ditemukan
    exit(); // Menghentikan eksekusi script
}

// Fetch data pembelian berdasarkan ID
$sql = "SELECT p.*, s.status, m.nama_metode AS metode_pembayaran FROM pembelian p 
        JOIN status s ON p.id_status = s.id_status 
        JOIN metode_pembayaran m ON p.id_metode = m.id_metode
        WHERE p.id_pembelian = ?"; // Query untuk mengambil data pembelian
$stmt = $conn->prepare($sql); // Menyiapkan statement
$stmt->bind_param("i", $id_pembelian); // Mengikat parameter untuk mencegah SQL injection
$stmt->execute(); // Menjalankan query
$pembelian = $stmt->get_result()->fetch_assoc(); // Mengambil hasil sebagai array asosiasi

// Pastikan data pembelian ditemukan
if (!$pembelian) {
    echo "<p>Data pembelian tidak ditemukan.</p>"; // Pesan jika data tidak ditemukan
    exit(); // Menghentikan eksekusi script
}

// Cek status dan metode pembayaran
$status = $pembelian['status']; // Menyimpan status pembelian
$metodePembayaran = isset($pembelian['metode_pembayaran']) ? $pembelian['metode_pembayaran'] : 'N/A'; // Menyimpan metode pembayaran atau 'N/A'

// Ambil total pembelian
$totalPembelian = $pembelian['total_pembelian']; // Menyimpan total pembelian

// Query untuk mengambil data bank
$bankQuery = "SELECT * FROM bank"; // Query untuk mengambil semua data bank
$bankResult = $conn->query($bankQuery); // Menjalankan query

// Inisialisasi variabel untuk pesan kesalahan atau sukses
$alertMessage = ''; // Variabel untuk pesan alert
$alertType = ''; // Tipe alert (sukses atau error)

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Memeriksa apakah form disubmit
    $id_pembelian = $_POST['id_pembelian']; // Mengambil ID pembelian dari form
    $id_bank = isset($_POST['nama_bank']) ? (int)$_POST['nama_bank'] : 0; // Validasi ID bank
    $jumlah = $totalPembelian; // Menggunakan total_pembelian langsung

    // Mengupload bukti pembayaran
    $uploader = new FileUploader("assets/bukti/"); // Inisialisasi uploader
    $bukti = $uploader->upload($_FILES['bukti']); // Memanggil fungsi upload

    if ($bukti) { // Jika upload berhasil
        // Jika upload berhasil, simpan data bukti pembayaran ke database
        $sql_insert = "INSERT INTO pembayaran (id_pembelian, id_bank, jumlah, bukti, tanggal_pembayaran) 
                       VALUES (?, ?, ?, ?, NOW())"; // Query untuk menyimpan bukti pembayaran
        $stmt_insert = $conn->prepare($sql_insert); // Menyiapkan statement
        $stmt_insert->bind_param("iiis", $id_pembelian, $id_bank, $jumlah, $bukti); // Mengikat parameter
    
        if ($stmt_insert->execute()) { // Menjalankan query insert
            // Setelah bukti tersimpan, update status pembelian menjadi "Diproses"
            $sql_update_status = "UPDATE pembelian SET id_status = (SELECT id_status FROM status WHERE status = 'Diproses') WHERE id_pembelian = ?"; // Query untuk mengupdate status
            $stmt_update_status = $conn->prepare($sql_update_status); // Menyiapkan statement
            $stmt_update_status->bind_param("i", $id_pembelian); // Mengikat parameter
            $stmt_update_status->execute(); // Menjalankan query update
    
            // Set success message in session after the form is submitted
            $_SESSION['alertMessage'] = "Bukti pembayaran berhasil diunggah dan status pembelian diperbarui menjadi Diproses!"; // Menyimpan pesan sukses ke sesi
            $_SESSION['alertType'] = "success"; // Tipe alert untuk sukses
    
            // Redirect to prevent form resubmission and show the alert
            header("Location: detail_pembayaran.php?id_pembelian=" . $id_pembelian); // Mengarahkan ke halaman detail pembayaran
            exit(); // Menghentikan eksekusi script
        } else {
            $alertMessage = "Terjadi kesalahan saat menyimpan data: " . $conn->error; // Pesan error jika gagal menyimpan
            $alertType = "danger"; // Tipe alert untuk error
            // Tambahkan log untuk debug
            echo "Database error: " . $conn->error; // Cek error dari database
        }
    } else {
        $alertMessage = "Error uploading file."; // Pesan error jika upload gagal
        $alertType = "danger"; // Tipe alert untuk error upload
    }
}

include 'header.php'; // Menyertakan header halaman
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Pembayaran</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YxwOcz27pIQob3hEmEbmFhAn3Uw4RzxZRzFy" crossorigin="anonymous">
</head>
<body>

<div class="container mt-4">
    <h1>Halaman Pembayaran</h1>
    
    <?php if ($alertMessage): ?>
        <div class="alert alert-<?= $alertType; ?>">
            <?= $alertMessage; ?>
        </div>
    <?php endif; ?>

    <h3>Detail Pembelian</h3>
    <p>Status: <?= htmlspecialchars($status); ?></p>
    <p>Metode Pembayaran: <?= htmlspecialchars($metodePembayaran); ?></p>
    <p>Total Pembelian: <?= htmlspecialchars($totalPembelian); ?></p>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_pembelian" value="<?= htmlspecialchars($id_pembelian); ?>">
        
        <div class="mb-3">
            <label for="nama_bank" class="form-label">Pilih Bank:</label>
            <select name="nama_bank" id="nama_bank" class="form-select" required>
                <option value="" disabled selected>Pilih Bank</option>
                <?php while ($row = $bankResult->fetch_assoc()): ?>
                    <option value="<?= $row['id_bank']; ?>"><?= htmlspecialchars($row['nama_bank']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="bukti" class="form-label">Unggah Bukti Pembayaran:</label>
            <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Bukti Pembayaran</button>
    </form>
</div>

<!-- Bootstrap JS dan dependensi Popper.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb+gd5c+R0YgztKZf7s7D8S+mDbr7Omy9sL9tiLZ4xuX9BQwF" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-pF8PcJ3C0PZ0e/1KQYYcxD1ShRA1GV8X0hI1lFfF5F6XtZ1gZTqa9Hc+RybU2G56" crossorigin="anonymous"></script>
</body>
</html>

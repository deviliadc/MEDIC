<?php
session_start(); // Memulai sesi untuk menyimpan data pengguna
include 'connect.php'; // Menghubungkan ke database
include 'header.php'; // Menyertakan file header

// Ambil data pengguna dari database berdasarkan username yang disimpan di sesi
$username = $_SESSION['username'];
$user_query = "SELECT * FROM customer WHERE username='$username'";
$user_result = $conn->query($user_query);
if (!$user_result) {
    die("Query failed: " . $conn->error); // Jika query gagal, tampilkan pesan error
}
if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $id_customer = $user['id_customer']; // Ambil ID customer dari hasil query
}

// Proses jika ada permintaan pembatalan pembelian (hapus data pembelian)
if (isset($_GET['batal_id'])) {
    $id_pembelian = $_GET['batal_id'];
    $delete_query = "DELETE FROM pembelian WHERE id_pembelian = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("i", $id_pembelian); // Mengikat parameter ID pembelian

    if ($stmt_delete->execute()) {
        // Set pesan sukses dan tipe alert jika pembatalan berhasil
        $_SESSION['alertMessage'] = "Pembelian berhasil dibatalkan.";
        $_SESSION['alertType'] = "success";
    } else {
        // Set pesan gagal dan tipe alert jika pembatalan gagal
        $_SESSION['alertMessage'] = "Gagal membatalkan pembelian.";
        $_SESSION['alertType'] = "danger";
    }
    $stmt_delete->close();
    header("Location: riwayat.php"); // Redirect ke halaman riwayat setelah pembatalan
    exit();
}

// Query untuk mengambil riwayat pembelian customer yang sedang login
$sql = "SELECT p.*, s.status, m.nama_metode 
        FROM pembelian p 
        JOIN status s ON p.id_status = s.id_status 
        JOIN metode_pembayaran m ON p.id_metode = m.id_metode
        WHERE p.id_customer = '$id_customer' 
        ORDER BY p.tanggal_pembelian DESC;";
$result = $conn->query($sql); // Jalankan query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Riwayat Checkout</h2>

    <!-- Tampilkan alert jika ada pesan (sukses atau gagal) -->
    <?php if (isset($_SESSION['alertMessage'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alertType']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['alertMessage']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alertMessage'], $_SESSION['alertType']); // Hapus pesan setelah ditampilkan ?>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal Pembelian</th>
                <th>Status</th>
                <th>Total Pembelian</th>
                <th>Resi</th>
                <th>Metode Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Periksa apakah ada data riwayat pembelian -->
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($row['tanggal_pembelian'])); ?></td>
                        <td>
                            <!-- Tampilkan status 'Diproses' untuk metode pembayaran COD -->
                            <?php if ($row['nama_metode'] == 'COD'): ?>
                                <?php echo 'Diproses'; ?>
                            <?php else: ?>
                                <?php echo htmlspecialchars($row['status']); ?>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?php echo number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['resi']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_metode']); ?></td>
                        <td>
                            <!-- Link untuk menampilkan nota dalam bentuk PDF -->
                            <a href="nota.php?id_pembelian=<?php echo $row['id_pembelian']; ?>" class="btn btn-primary" target="_blank">Nota (PDF)</a>

                            <!-- Query untuk memeriksa apakah sudah ada pembayaran -->
                            <?php
                            $pembayaran_query = "SELECT * FROM pembayaran WHERE id_pembelian = ?";
                            $stmt = $conn->prepare($pembayaran_query);
                            $stmt->bind_param("i", $row['id_pembelian']); // Mengikat ID pembelian
                            $stmt->execute();
                            $pembayaran_result = $stmt->get_result();
                            $stmt->close();
                            ?>

                            <!-- Tampilkan tombol input atau lihat pembayaran berdasarkan status pembayaran -->
                            <?php if ($row['nama_metode'] != 'COD'): ?>
                                <?php if ($row['status'] == 'Belum Dibayar' && $pembayaran_result->num_rows == 0): ?>
                                    <a href="pembayaran.php?id_pembelian=<?php echo $row['id_pembelian']; ?>" class="btn btn-warning">Input Pembayaran</a>
                                <?php else: ?>
                                    <a href="detail_pembayaran.php?id_pembelian=<?php echo $row['id_pembelian']; ?>" class="btn btn-info">Lihat Pembayaran</a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Tampilkan tombol batal jika status pembelian masih belum dibayar -->
                            <?php if ($row['status'] == 'Belum Dibayar'): ?>
                                <a href="riwayat.php?batal_id=<?php echo $row['id_pembelian']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pembelian ini?');">Batal</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Tampilkan pesan jika tidak ada riwayat pembelian -->
                <tr>
                    <td colspan="6" class="text-center">Belum ada riwayat pembelian.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

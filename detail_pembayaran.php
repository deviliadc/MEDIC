<?php
session_start();
include 'connect.php';

// Ambil ID pembelian dari query parameter
$id_pembelian = isset($_GET['id_pembelian']) ? $_GET['id_pembelian'] : null;

// Ambil detail pembayaran dan data pengiriman berdasarkan ID pembelian
$sql = "SELECT p.*, s.status, b.nama_bank, b.rekening, pb.nama_penerima, pb.alamat_penerima, pb.telp_penerima, pb.resi
        FROM pembayaran p 
        JOIN pembelian pb ON p.id_pembelian = pb.id_pembelian 
        JOIN status s ON pb.id_status = s.id_status 
        JOIN bank b ON p.id_bank = b.id_bank 
        WHERE p.id_pembelian = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pembelian);
$stmt->execute();
$result = $stmt->get_result();
$pembayaran = $result->fetch_assoc();
$stmt->close();

// Pastikan data pembayaran ditemukan
if (!$pembayaran) {
    echo "<p>Data pembayaran tidak ditemukan.</p>";
    exit();
}

// Ambil detail produk yang dibeli
$sql_produk = "SELECT dp.*, p.nama_produk, p.harga_produk 
                FROM detail_pembelian dp 
                JOIN produk p ON dp.id_produk = p.id_produk 
                WHERE dp.id_pembelian = ?";
$stmt_produk = $conn->prepare($sql_produk);
$stmt_produk->bind_param("i", $id_pembelian);
$stmt_produk->execute();
$result_produk = $stmt_produk->get_result();
$produk_dibeli = $result_produk->fetch_all(MYSQLI_ASSOC);
$stmt_produk->close();

// Cek jika bukti pembayaran sudah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_bank = isset($_POST['nama_bank']) ? (int)$_POST['nama_bank'] : 0; // Validasi ID bank
    $jumlah = $pembayaran['jumlah']; // Gunakan jumlah dari data pembayaran yang sudah diambil

    // Mengupload bukti pembayaran
    $bukti = $_FILES['bukti']['name']; // Mengambil nama file
    $lokasi = $_FILES['bukti']['tmp_name']; // Mengambil lokasi sementara file
    $target_dir = "assets/bukti/"; // Direktori tempat menyimpan file
    $target_file = $target_dir . basename($bukti); // Menggabungkan path

    // Cek apakah folder target ada, jika tidak buat folder tersebut
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat folder dengan izin tulis
    }

    // Validasi upload file (hanya menerima file gambar)
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($lokasi, $target_file)) {
            // Jika upload berhasil, simpan data bukti pembayaran ke database
            $sql_insert = "INSERT INTO pembayaran (id_pembelian, id_bank, jumlah, bukti, tanggal_pembayaran) 
                           VALUES (?, ?, ?, ?, NOW())"; // Use prepared statements
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiis", $id_pembelian, $id_bank, $jumlah, $bukti); // Bind parameters
            
            if ($stmt_insert->execute()) {
                // Update status pembelian ke "Proses"
                $sql_update = "UPDATE pembelian SET id_status = (SELECT id_status FROM status WHERE status = 'Proses') WHERE id_pembelian = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("i", $id_pembelian);
                $stmt_update->execute();
                $stmt_update->close();

                // Set success message in session
                $_SESSION['alertMessage'] = "Bukti pembayaran berhasil diunggah! Status pembayaran diperbarui ke 'Proses'.";
                $_SESSION['alertType'] = "success"; // Tipe alert untuk sukses
                // Redirect to detail payment page
                header("Location: detail_pembayaran.php?id_pembelian=" . $id_pembelian);
                exit();
            } else {
                echo "Terjadi kesalahan saat menyimpan data bukti pembayaran: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Hanya file gambar yang diperbolehkan untuk diunggah.";
    }
}

include 'header.php'; // Menyertakan header halaman
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Detail Pembayaran</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4>ID Pembelian: <?php echo htmlspecialchars($pembayaran['id_pembelian']); ?></h4>
                    <p><strong>Status Pembelian:</strong> <?php echo htmlspecialchars($pembayaran['status']); ?></p>
                    <p><strong>Nama Bank:</strong> <?php echo htmlspecialchars($pembayaran['nama_bank']); ?> (Rekening: <?php echo htmlspecialchars($pembayaran['rekening']); ?>)</p>
                    <p><strong>Jumlah Pembayaran:</strong> Rp <?php echo number_format($pembayaran['jumlah'], 0, ',', '.'); ?></p>
                    <p><strong>Tanggal Pembayaran:</strong> <?php echo date('d-m-Y', strtotime($pembayaran['tanggal_pembayaran'])); ?></p>

                    <h4>Bukti Pembayaran:</h4>
                    <img src="assets/bukti/<?php echo htmlspecialchars($pembayaran['bukti']); ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 300px; max-height: 300px;">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4>Data Pengiriman:</h4>
                    <p>Nama Penerima: <?php echo htmlspecialchars($pembayaran['nama_penerima']); ?></p>
                    <p>Alamat Penerima: <?php echo htmlspecialchars($pembayaran['alamat_penerima']); ?></p>
                    <p>Telepon Penerima: <?php echo htmlspecialchars($pembayaran['telp_penerima']); ?></p>
                    <p>Nomor Resi: <?php echo htmlspecialchars($pembayaran['resi']); ?></p>

                    <h4>Detail Produk yang Dibeli:</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_semua = 0; // Variabel untuk menyimpan total semua harga
                            foreach ($produk_dibeli as $item): 
                                $total = $item['harga_produk'] * $item['quantity']; // Hitung total harga per produk
                                $total_semua += $total; // Tambahkan ke total semua
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                <td>Rp <?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Keseluruhan:</th>
                                <th>Rp <?php echo number_format($total_semua, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="riwayat.php" class="btn btn-primary">Kembali</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
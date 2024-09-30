<?php
include '../connect.php'; // Menghubungkan ke file koneksi database
include 'header.php'; // Menyertakan header

// Ambil ID pembelian dari parameter query
$id_pembelian = isset($_GET['id_pembelian']) ? $_GET['id_pembelian'] : null;

// Mengambil data pembayaran dan pembelian berdasarkan ID pembelian
$sql = "SELECT pb.id_pembelian, pb.id_customer, pb.tanggal_pembelian, pb.nama_penerima, pb.alamat_penerima, pb.telp_penerima, pb.total_pembelian, pb.id_metode, pb.id_status,
            s.status, p.jumlah, p.bukti, p.tanggal_pembayaran, b.nama_bank, b.rekening, pb.resi
        FROM pembelian pb
        JOIN pembayaran p ON pb.id_pembelian = p.id_pembelian
        JOIN status s ON pb.id_status = s.id_status
        JOIN bank b ON p.id_bank = b.id_bank
        WHERE pb.id_pembelian = ?"; // Menyusun query untuk mengambil data
$stmt = $conn->prepare($sql); // Menyiapkan pernyataan
$stmt->bind_param("i", $id_pembelian); // Mengikat parameter
$stmt->execute(); // Menjalankan pernyataan
$result = $stmt->get_result(); // Mengambil hasil
$pembelian = $result->fetch_assoc(); // Mengambil data pembelian
$stmt->close(); // Menutup pernyataan

// Memeriksa apakah data pembelian ditemukan
if (!$pembelian) {
    echo "<div class='container mt-5'><h3 class='text-center'>Data Pembelian tidak ditemukan.</h3></div>";
    exit; // Menghentikan eksekusi jika tidak ada data
}

// Mengambil detail produk yang dibeli
$sql_produk = "SELECT dp.*, p.nama_produk, p.harga_produk 
                FROM detail_pembelian dp 
                JOIN produk p ON dp.id_produk = p.id_produk 
                WHERE dp.id_pembelian = ?"; // Menyusun query untuk mengambil detail produk
$stmt_produk = $conn->prepare($sql_produk); // Menyiapkan pernyataan
$stmt_produk->bind_param("i", $id_pembelian); // Mengikat parameter
$stmt_produk->execute(); // Menjalankan pernyataan
$result_produk = $stmt_produk->get_result(); // Mengambil hasil
$produk_dibeli = $result_produk->fetch_all(MYSQLI_ASSOC); // Mengambil semua data produk
$stmt_produk->close(); // Menutup pernyataan
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-wrapper {
            margin-left: 250px; /* penyesuaian lebar sidebar */
            padding: 20px; /* padding untuk penataan yang lebih baik */
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?> <!-- Menyertakan sidebar -->
<div class="content-wrapper">
        <div class="col">
            <h2 class="text-center">Detail Pembelian</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>ID Pembelian: <?php echo htmlspecialchars($pembelian['id_pembelian']); ?></h4>
                            <p><strong>ID Customer:</strong> <?php echo htmlspecialchars($pembelian['id_customer']); ?></p>
                            <p><strong>Tanggal Pembelian:</strong> <?php echo date('d-m-Y', strtotime($pembelian['tanggal_pembelian'])); ?></p>
                            <p><strong>Nama Penerima:</strong> <?php echo htmlspecialchars($pembelian['nama_penerima']); ?></p>
                            <p><strong>Alamat Penerima:</strong> <?php echo htmlspecialchars($pembelian['alamat_penerima']); ?></p>
                            <p><strong>Telp Penerima:</strong> <?php echo htmlspecialchars($pembelian['telp_penerima']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p><strong>Status Pembelian:</strong> <?php echo htmlspecialchars($pembelian['status']); ?></p>
                            <p><strong>Nama Bank:</strong> <?php echo htmlspecialchars($pembelian['nama_bank']); ?> (Rekening: <?php echo htmlspecialchars($pembelian['rekening']); ?>)</p>
                            <p><strong>Jumlah Pembayaran:</strong> Rp <?php echo number_format($pembelian['jumlah'], 0, ',', '.'); ?></p>
                            <p><strong>Tanggal Pembayaran:</strong> <?php echo date('d-m-Y', strtotime($pembelian['tanggal_pembayaran'])); ?></p>
                            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($pembelian['id_metode']); ?></p>
                            <p><strong>ID Status:</strong> <?php echo htmlspecialchars($pembelian['id_status']); ?></p>
                            <p><strong>No. Resi:</strong> <?php echo htmlspecialchars($pembelian['resi']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Detail Produk yang Dibeli:</h4>
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
            <p><strong>Total Pembelian:</strong> Rp <?php echo number_format($pembelian['total_pembelian'], 0, ',', '.'); ?></p>

            <div class="mt-4">
                <a href="penjualan.php" class="btn btn-primary">Kembali</a> <!-- Tombol untuk kembali -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

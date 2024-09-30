<?php
include '../connect.php';
include 'header.php'; 

// Variabel untuk tanggal awal dan akhir
$tgl_awal = isset($_POST['tglawal']) ? $_POST['tglawal'] : '';
$tgl_akhir = isset($_POST['tglakhir']) ? $_POST['tglakhir'] : '';

// Buat query berdasarkan tanggal yang dipilih
$sqlPenjualan = "SELECT pb.id_pembelian, pb.tanggal_pembelian, c.username, SUM(pb.total_pembelian) AS total_pembelian, s.status, pb.resi
                FROM pembelian pb 
                JOIN customer c ON pb.id_customer = c.id_customer 
                JOIN status s ON pb.id_status = s.id_status ";

if ($tgl_awal && $tgl_akhir) {
    $sqlPenjualan .= "WHERE pb.tanggal_pembelian BETWEEN '$tgl_awal' AND '$tgl_akhir' ";
}

$sqlPenjualan .= "GROUP BY pb.id_pembelian, c.username, pb.tanggal_pembelian, s.status, pb.resi
                ORDER BY pb.tanggal_pembelian DESC";

$resultPenjualan = $conn->query($sqlPenjualan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS untuk background header tabel */
        .table thead th {
            background-color: gray; /* Warna latar belakang */
            color: white; /* Warna teks */
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col" style="margin-left: 250px;">
            <div class="container mt-5">
                <h2 class="text-center">Data Penjualan</h2>

                <form method="post">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Tanggal Awal</label>
                                <input type="date" class="form-control" name="tglawal" value="<?php echo htmlspecialchars($tgl_awal); ?>">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tglakhir" value="<?php echo htmlspecialchars($tgl_akhir); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-primary" name="kirim">Lihat</button>
                        </div>
                    </div>    
                </form>

                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>ID Pembelian</th>
                            <th>Tanggal Pembelian</th>
                            <th>Nama Customer</th>
                            <th>Total Pembelian</th>
                            <th>Status</th>
                            <th>Resi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultPenjualan->num_rows > 0): ?>
                            <?php while ($row = $resultPenjualan->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_pembelian']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_pembelian'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td>Rp <?php echo number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['resi']); ?></td>
                                    <td>
                                        <a href="pembayaran.php?id_pembelian=<?php echo $row['id_pembelian']; ?>" class="btn btn-warning">Pembayaran</a>
                                        <a href="detail.php?id_pembelian=<?php echo $row['id_pembelian']; ?>" class="btn btn-primary">Detail</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data penjualan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

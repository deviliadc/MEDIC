<?php
include '../connect.php';
include 'header.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID pembelian dari parameter URL
$id_pembelian = isset($_GET['id_pembelian']) ? intval($_GET['id_pembelian']) : 0;

// Ambil data pembelian berdasarkan ID
$sql = "SELECT pb.*, c.username, s.status, p.bukti
        FROM pembelian pb 
        JOIN customer c ON pb.id_customer = c.id_customer 
        JOIN pembayaran p ON p.id_pembelian = pb.id_pembelian
        JOIN status s ON pb.id_status = s.id_status
        WHERE pb.id_pembelian = ?";

// Prepare SQL statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL preparation failed: " . $conn->connect_error);
}
$stmt->bind_param("i", $id_pembelian);
$stmt->execute();
$result = $stmt->get_result();

$pembelian = $result->fetch_assoc();

// Periksa apakah data pembelian ditemukan
if (!$pembelian) {
    echo "<div class='container mt-5'><h3 class='text-center'>Data Pembelian tidak ditemukan.</h3></div>";
    exit;
}

// Ambil data status untuk dropdown
$sql_status = "SELECT * FROM status";
$result_status = $conn->query($sql_status);

// Proses perubahan status dan resi jika ada pengiriman form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    $resi = $_POST['resi'];
    
    $update_sql = "UPDATE pembelian SET id_status = ?, resi = ? WHERE id_pembelian = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    if (!$update_stmt) {
        die("Update SQL preparation failed: " . $conn->connect_error);
    }
    
    $update_stmt->bind_param("isi", $new_status, $resi, $id_pembelian);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Status pembelian dan resi berhasil diperbarui.'); window.location.href='penjualan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status dan resi.');</script>";
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-wrapper {
            margin-left: 250px; /* adjust according to your sidebar width */
            padding: 20px; /* padding for better spacing */
        }
        .btn-close {
            position: absolute;
            right: 20px;
            top: 20px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content-wrapper">
    <h2 class="text-center">Edit Pembayaran</h2>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>ID Pembelian</th>
                <td><?php echo htmlspecialchars($pembelian['id_pembelian']) ?: '-'; ?></td>
            </tr>
            <tr>
                <th>Tanggal Pembelian</th>
                <td><?php echo !empty($pembelian['tanggal_pembelian']) ? date('d-m-Y', strtotime($pembelian['tanggal_pembelian'])) : '-'; ?></td>
            </tr>
            <tr>
                <th>Nama Customer</th>
                <td><?php echo htmlspecialchars($pembelian['username']) ?: '-'; ?></td>
            </tr>
            <tr>
                <th>Total Pembelian</th>
                <td><?php echo !empty($pembelian['total_pembelian']) ? 'Rp ' . number_format($pembelian['total_pembelian'], 0, ',', '.') : '-'; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <form method="post">
                        <select name="status" class="form-control">
                            <?php while ($status = $result_status->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($status['id_status']); ?>" <?php echo ($pembelian['id_status'] == $status['id_status']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($status['status']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="mt-2">
                            <label for="resi">No. Resi:</label>
                            <input type="text" name="resi" id="resi" class="form-control" value="<?php echo htmlspecialchars($pembelian['resi']) ?: ''; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-2">Update Status dan Resi</button>
                    </form>
                </td>
            </tr>
            <tr>
                <th>Bukti Pembayaran</th>
                <td>
                    <?php if ($pembelian['id_status'] != 1): // Check if status is not COD ?>
                        <?php if (!empty($pembelian['bukti'])): ?>
                            <img src="../assets/bukti/<?php echo htmlspecialchars($pembelian['bukti']); ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 300px;">
                        <?php else: ?>
                            <p>Bukti pembayaran belum diupload.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Status pembayaran menggunakan COD, bukti pembayaran tidak diperlukan.</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-4">
        <a href="penjualan.php" class="btn btn-primary">Kembali</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

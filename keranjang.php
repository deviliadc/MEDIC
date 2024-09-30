<?php
session_start();
include 'connect.php'; 
include 'header.php'; 

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Check if an item is being added
if (isset($_GET['id_produk']) && isset($_GET['action']) && $_GET['action'] == 'add') {
    $id_produk = $_GET['id_produk'];

    // Check if the item is already in the keranjang
    if (isset($_SESSION['keranjang'][$id_produk])) {
        // Increase the jumlah if the product already exists
        $_SESSION['keranjang'][$id_produk]['jumlah']++;
    } else {
        // Get product details from the database
        $sql = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            // Add item to keranjang
            $_SESSION['keranjang'][$id_produk] = [
                'id_produk' => $product['id_produk'],
                'nama_produk' => $product['nama_produk'],
                'harga' => $product['harga_produk'],
                'jumlah' => 1
            ];
        }
    }

    // Redirect to the keranjang page
    header("Location: keranjang.php");
    exit();
}

// Check if updating jumlah
if (isset($_POST['update'])) {
    foreach ($_SESSION['keranjang'] as $id_produk => $item) {
        if (isset($_POST['jumlah'][$id_produk])) {
            $_SESSION['keranjang'][$id_produk]['jumlah'] = max(1, intval($_POST['jumlah'][$id_produk])); // Set to 1 if less than 1
        }
    }
}

// Check if removing an item
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id_produk = $_GET['id_produk'];
    unset($_SESSION['keranjang'][$id_produk]);
    header("Location: keranjang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h2>Keranjang</h2>
        <a href="produk.php" class="btn-close" aria-label="Close"></a>
    </div>
    <?php if (empty($_SESSION['keranjang'])): ?>
        <p class="alert alert-warning">Anda belum memiliki barang di keranjang.</p>
        <a href="produk.php" class="btn btn-primary">Lanjutkan Belanja</a>
    <?php else: ?>
        <form method="POST" action="keranjang.php">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $no = 1; // Numbering start
                    foreach ($_SESSION['keranjang'] as $item) {
                        $subtotal = $item['harga'] * $item['jumlah'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td> <!-- Incrementing number -->
                            <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                            <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <input type="number" name="jumlah[<?php echo $item['id_produk']; ?>]" value="<?php echo $item['jumlah']; ?>" min="1" class="form-control" style="width: 80px; display: inline;">
                            </td>
                            <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td>
                                <a href="keranjang.php?id_produk=<?php echo $item['id_produk']; ?>&action=remove" class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button type="submit" name="update" class="btn btn-warning">Update Keranjang</button>
        </form>
        <h4>Total Belanja: Rp <?php echo number_format($total, 0, ',', '.'); ?></h4>
        <?php $_SESSION['total_belanja']= $total; ?>
        <a href="checkout.php" class="btn btn-success">Checkout</a>
        <a href="produk.php" class="btn btn-primary">Lanjutkan Belanja</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

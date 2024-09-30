<?php 
session_start();
include 'connect.php'; 
include 'header.php'; 

// Ambil data produk dari database dengan pencarian
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT p.id_produk, p.nama_produk, p.harga_produk, p.foto_produk 
            FROM produk p 
            JOIN kategori k ON p.id_kategori = k.id_kategori 
            WHERE p.nama_produk LIKE '%" . $conn->real_escape_string($search) . "%' 
            OR k.nama_kategori LIKE '%" . $conn->real_escape_string($search) . "%'";
} else {
    $sql = "SELECT id_produk, nama_produk, harga_produk, foto_produk FROM produk";
}

$result = $conn->query($sql);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            width: 250px;
            height: 250px; /* Tentukan tinggi maksimal gambar */
            object-fit: cover; /* Mempertahankan proporsi gambar dengan crop */
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Produk Kami</h2>

    <!-- Form Pencarian -->
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <div class="row">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <img src="assets/foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                            <p class="card-text">Harga: Rp <?php echo number_format($product['harga_produk'], 0, ',', '.'); ?></p>
                        </div>
                        <div>
                            <a href="produk_detail.php?id_produk=<?php echo $product['id_produk']; ?>" class="btn btn-primary">Detail</a>
                            <?php if (isset($_SESSION['username'])): // Periksa apakah pengguna sudah login ?>
                                <a href="keranjang.php?id_produk=<?php echo $product['id_produk']; ?>&action=add" class="btn btn-light">
                                    <i class="bi bi-cart-plus-fill"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>

<?php
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_GET['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}
?>

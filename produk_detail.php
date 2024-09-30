<?php 
session_start();
include 'connect.php'; 

// Ambil id_produk dari URL
$id_produk = isset($_GET['id_produk']) ? (int)$_GET['id_produk'] : 0;

// Ambil data produk dari database
$sql = "SELECT p.id_produk, p.nama_produk, p.harga_produk, p.berat_produk, p.foto_produk, p.deskripsi_produk, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = p.id_kategori 
        WHERE id_produk = $id_produk";

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Check if the product exists
if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h2>Produk tidak ditemukan.</h2></div>";
    include 'footer.php'; 
    exit();
}

$product = $result->fetch_assoc();

include 'header.php'; // Menyertakan header halaman
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .img-fluid {
            width: 450px;
            height: 450px; /* Tentukan tinggi maksimal gambar */
            object-fit: cover; /* Mempertahankan proporsi gambar dengan crop */
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2><?php echo htmlspecialchars($product['nama_produk']); ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <img src="assets/foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
        </div>
        <div class="col-md-6">
            <h5>Harga: Rp <?php echo number_format($product['harga_produk'], 0, ',', '.'); ?></h5>
            <p>Berat: <?php echo htmlspecialchars($product['berat_produk']); ?> gram</p>
            <p>Kategori: <?php echo htmlspecialchars($product['nama_kategori']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['deskripsi_produk'])); ?></p>
            <?php if (isset($_SESSION['username'])): // Periksa apakah pengguna sudah login ?>
                <a href="keranjang.php?id_produk=<?php echo $product['id_produk']; ?>&action=add" class="btn btn-light">
                    <i class="bi bi-cart-plus-fill"></i> Tambah ke Keranjang
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QiT7gnpGVJO7zlZmThBXzBAsJtnszQfjEl4G6UrfMvhSz4rA/S98IbxJgtHlntq+" crossorigin="anonymous"></script>
</body>
</html>

<?php include 'footer.php'; ?>

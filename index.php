<?php 
session_start();
include 'connect.php'; 

// Ambil data produk dari database
$sql = "SELECT id_produk, nama_produk, harga_produk, foto_produk FROM produk LIMIT 9";
$result = $conn->query($sql);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . $conn->error);
}

include 'header.php'; // Menyertakan header halaman
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kami</title>
    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card-img-top {
            width: 250px;
            height: 250px; /* Tentukan tinggi maksimal gambar */
            object-fit: cover; /* Mempertahankan proporsi gambar dengan crop */
        }
    </style>
</head>
<body>
<!-- Banner Carousel -->
<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.pexels.com/photos/5673523/pexels-photo-5673523.jpeg" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Banner 1">
            <div class="carousel-caption d-none d-md-block">
                <h5>Selamat Datang di MEDIC</h5>
                <p>Dapatkan alat kesehatan, obat dan suplemen terbaik dengan harga terjangkau.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.pexels.com/photos/3683067/pexels-photo-3683067.jpeg" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Banner 2">
            <div class="carousel-caption d-none d-md-block">
                <h5>Layanan Terbaik</h5>
                <p>Pesan sekarang dan kami akan mengantarkan produk ke rumah Anda.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.pexels.com/photos/7722673/pexels-photo-7722673.jpeg" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Banner 3">
            <div class="carousel-caption d-none d-md-block">
                <h5>Free Ongkir</h5>
                <p>Untuk daerah Surabaya dan Sekitarnya gratis biaya pengiriman.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Content Section -->
<div class="container mt-4" style="padding-top: 60px;"> <!-- Added padding to prevent overlap with header -->
    <div class="jumbotron">
        <h1 class="display-4">Selamat Datang di MEDIC</h1>
        <p class="lead">Kami menyediakan berbagai macam alat kesehatan, obat dan suplemen berkualitas.</p>
    </div>

    <!-- Products Section -->
    <h2 class="mt-5">Produk Kami</h2>
    <div class="row">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <img src="assets/foto_produk/<?php echo htmlspecialchars($product['foto_produk']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="card-img-top">
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
    <div class="col-md-4">
        <a class="btn btn-primary btn-lg" href="produk.php" role="button">Lihat Produk Lainnya</a>
    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QiT7gnpGVJO7zlZmThBXzBAsJtnszQfjEl4G6UrfMvhSz4rA/S98IbxJgtHlntq+" crossorigin="anonymous"></script>
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

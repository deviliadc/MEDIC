<?php
session_start(); // Memulai sesi untuk mengakses variabel sesi
include 'connect.php'; // Menyertakan file koneksi database
include 'header.php'; // Menyertakan file header untuk layout halaman

// Mengambil metode pembayaran dari database
$metode_query = "SELECT * FROM metode_pembayaran"; // Kuery untuk memilih semua metode pembayaran
$metode_result = $conn->query($metode_query); // Menjalankan kuery
if (!$metode_result) { // Memeriksa apakah kuery gagal
    die("Query failed: " . $conn->error); // Menangani kesalahan kuery
}

// Mengambil data pengguna dari database
$username = $_SESSION['username']; // Mengambil username dari sesi
$user_query = "SELECT * FROM customer WHERE username='$username'"; // Kuery untuk mendapatkan data pengguna
$user_result = $conn->query($user_query); // Menjalankan kuery
if (!$user_result) { // Memeriksa apakah kuery gagal
    die("Query failed: " . $conn->error); // Menangani kesalahan kuery
}
if ($user_result->num_rows > 0) { // Memeriksa apakah data pengguna ditemukan
    $user = $user_result->fetch_assoc(); // Mengambil data pengguna sebagai array asosiatif
    $id_customer = $user['id_customer']; // Mengambil ID pelanggan
}

// Menangani pengiriman formulir untuk proses checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Memeriksa apakah metode permintaan adalah POST
    $id_metode = $_POST['metode']; // Mengambil metode pembayaran yang dipilih dari formulir
    $nama_penerima = $_POST['name']; // Mengambil nama penerima dari formulir
    $alamat_penerima = $_POST['address']; // Mengambil alamat penerima dari formulir
    $telp_penerima = $_POST['phone']; // Mengambil nomor telepon penerima dari formulir

    // Validasi dan proses pembelian
    if (empty($_SESSION['keranjang'])) { // Memeriksa apakah keranjang kosong
        header('Location: keranjang.php?message=Keranjang%20kosong%20tidak%20dapat%20melanjutkan%20pembelian'); // Redirect dengan pesan
        exit(); // Menghentikan eksekusi skrip lebih lanjut
    }

    $total_pembelian = $_SESSION['total_belanja']; // Mengambil total jumlah pembelian dari sesi
    $current_date = date("Y-m-d"); // Mengambil tanggal saat ini

    // Menentukan status pesanan
    $id_status = 1; // Set status menjadi 'Belum Dibayar' (id_status = 1) untuk semua metode pembayaran

    // Menyisipkan catatan pembelian ke dalam tabel pembelian
    $sql_pembelian = "INSERT INTO pembelian (id_customer, tanggal_pembelian, total_pembelian, id_metode, 
                                nama_penerima, alamat_penerima, telp_penerima, id_status) 
            VALUES ('$id_customer', '$current_date', '$total_pembelian', '$id_metode',
                    '$nama_penerima', '$alamat_penerima', '$telp_penerima', '$id_status')";
    
    if ($conn->query($sql_pembelian) === TRUE) { // Memeriksa apakah kuery sisip berhasil
        $id_pembelian = $conn->insert_id; // Mengambil ID pembelian terakhir yang disisipkan
    
        // Memproses detail checkout untuk setiap item dalam keranjang
        foreach ($_SESSION['keranjang'] as $item) { // Melakukan iterasi melalui setiap item dalam keranjang
            $id_produk = $item['id_produk']; // Mengambil ID produk dari item
            $quantity = $item['jumlah']; // Mengambil jumlah dari item
            $harga = $item['harga'] * $quantity; // Menghitung total harga untuk item
    
            // Menyisipkan ke dalam tabel detail_pembelian
            $sql_detail = "INSERT INTO detail_pembelian (id_pembelian, id_produk, quantity, harga) 
                            VALUES ('$id_pembelian', '$id_produk', '$quantity', '$harga')";
            $conn->query($sql_detail); // Menjalankan kuery sisip
        }
    
        // Mengosongkan keranjang setelah pembelian
        unset($_SESSION['keranjang']); // Menghapus variabel sesi keranjang
    
        // Redirect ke halaman riwayat pembelian dengan pesan sukses
        header('Location: riwayat.php?message=Pembelian%20berhasil,%20silahkan%20lakukan%20pembayaran.');
        exit(); // Menghentikan eksekusi skrip lebih lanjut
    } else {
        echo "Error: " . $sql_pembelian . "<br>" . $conn->error; // Menampilkan kesalahan jika sisip gagal
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Menyertakan CSS Bootstrap -->
</head>
<body>
    <div class="container mt-4"> <!-- Kontainer utama untuk formulir checkout -->
        <h2>Checkout</h2> <!-- Header bagian checkout -->
        <form action="checkout.php" method="post"> <!-- Form untuk input pengguna -->
            <div class="mb-3"> <!-- Input untuk nama lengkap -->
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" name="name" required> <!-- Input nama lengkap -->
            </div>
            <div class="mb-3"> <!-- Input untuk alamat -->
                <label for="address" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="address" name="address" required> <!-- Input alamat -->
            </div>
            <div class="mb-3"> <!-- Input untuk nomor telepon -->
                <label for="phone" class="form-label">Telepon</label>
                <input type="text" class="form-control" id="phone" name="phone" required> <!-- Input telepon -->
            </div>
            <div class="mb-3"> <!-- Dropdown untuk pemilihan metode pembayaran -->
                <label for="metode" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="metode" name="metode" required> <!-- Dropdown metode pembayaran -->
                    <option value="">Pilih Metode</option> <!-- Opsi placeholder -->
                    <?php while ($row = $metode_result->fetch_assoc()): ?> <!-- Melakukan iterasi melalui metode pembayaran -->
                        <option value="<?php echo $row['id_metode']; ?>"><?php echo htmlspecialchars($row['nama_metode']); ?></option> <!-- Menampilkan setiap metode pembayaran -->
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Konfirmasi Pembelian</button> <!-- Tombol untuk mengirim formulir -->
        </form>
    </div>
    <?php include 'footer.php'; ?> <!-- Menyertakan file footer -->
</body>
</html>

<?php
session_start(); // Memulai sesi untuk menyimpan pesan alert
include 'connect.php'; // Menyertakan file koneksi ke database
include 'header.php'; // Menyertakan header halaman

// Fungsi untuk menambahkan angka di belakang nama file jika sudah ada
function uniqueFileName($target_dir, $fileName) {
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION); // Mendapatkan tipe file (ekstensi)
    $baseName = pathinfo($fileName, PATHINFO_FILENAME); // Mendapatkan nama file tanpa ekstensi
    $newFileName = $fileName; // Inisialisasi nama file baru
    $counter = 1; // Mulai dengan angka 1

    // Cek apakah file sudah ada di direktori target
    while (file_exists($target_dir . $newFileName)) {
        // Jika ada, tambahkan angka di belakang nama file sebelum ekstensi
        $newFileName = $baseName . '-' . $counter . '.' . $fileType;
        $counter++; // Tingkatkan angka
    }

    return $newFileName; // Kembalikan nama file yang unik
}

// Ambil ID pembelian dari query parameter
$id_pembelian = isset($_GET['id_pembelian']) ? $_GET['id_pembelian'] : null;

// Pastikan ID pembelian ada
if ($id_pembelian === null) {
    echo "<p>ID pembelian tidak ditemukan.</p>"; // Pesan jika ID tidak ditemukan
    exit(); // Menghentikan eksekusi script
}

// Fetch data pembelian berdasarkan ID
$sql = "SELECT p.*, s.status, m.nama_metode AS metode_pembayaran FROM pembelian p 
        JOIN status s ON p.id_status = s.id_status 
        JOIN metode_pembayaran m ON p.id_metode = m.id_metode
        WHERE p.id_pembelian = ?"; // Query untuk mengambil data pembelian
$stmt = $conn->prepare($sql); // Menyiapkan statement
$stmt->bind_param("i", $id_pembelian); // Mengikat parameter untuk mencegah SQL injection
$stmt->execute(); // Menjalankan query
$pembelian = $stmt->get_result()->fetch_assoc(); // Mengambil hasil sebagai array asosiasi

// Pastikan data pembelian ditemukan
if (!$pembelian) {
    echo "<p>Data pembelian tidak ditemukan.</p>"; // Pesan jika data tidak ditemukan
    exit(); // Menghentikan eksekusi script
}

// Cek status dan metode pembayaran
$status = $pembelian['status']; // Menyimpan status pembelian
$metode_pembayaran = isset($pembelian['metode_pembayaran']) ? $pembelian['metode_pembayaran'] : 'N/A'; // Menyimpan metode pembayaran atau 'N/A'

// Ambil total pembelian
$total_pembelian = $pembelian['total_pembelian']; // Menyimpan total pembelian

// Query untuk mengambil data bank
$bank_query = "SELECT * FROM bank"; // Query untuk mengambil semua data bank
$bank_result = $conn->query($bank_query); // Menjalankan query

// Inisialisasi variabel untuk pesan kesalahan atau sukses
$alertMessage = ''; // Variabel untuk pesan alert
$alertType = ''; // Tipe alert (sukses atau error)

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Memeriksa apakah form disubmit
    $id_pembelian = $_POST['id_pembelian']; // Mengambil ID pembelian dari form
    $id_bank = isset($_POST['nama_bank']) ? (int)$_POST['nama_bank'] : 0; // Validasi ID bank
    $jumlah = $total_pembelian; // Menggunakan total_pembelian langsung

    // Mengupload bukti pembayaran
    $bukti = $_FILES['bukti']['name']; // Mengambil nama file
    $lokasi = $_FILES['bukti']['tmp_name']; // Mengambil lokasi sementara file
    $target_dir = "assets/bukti/"; // Direktori tempat menyimpan file

    // Cek apakah folder target ada, jika tidak buat folder tersebut
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat folder dengan izin tulis
    }

    // Cek dan buat nama file yang unik
    $bukti = uniqueFileName($target_dir, $bukti);
    $target_file = $target_dir . $bukti; // Gabungkan path lengkap

    // Validasi upload file (hanya menerima file gambar)
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Mengambil tipe file
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // Daftar tipe file yang diperbolehkan

    if (in_array($fileType, $allowedTypes)) { // Memeriksa apakah tipe file diperbolehkan
        if (move_uploaded_file($lokasi, $target_file)) { // Mengupload file ke direktori tujuan
            // Jika upload berhasil, simpan data bukti pembayaran ke database
            $sql_insert = "INSERT INTO pembayaran (id_pembelian, id_bank, jumlah, bukti, tanggal_pembayaran) 
                           VALUES (?, ?, ?, ?, NOW())"; // Query untuk menyimpan bukti pembayaran
            $stmt_insert = $conn->prepare($sql_insert); // Menyiapkan statement
            $stmt_insert->bind_param("iiis", $id_pembelian, $id_bank, $jumlah, $bukti); // Mengikat parameter
        
            if ($stmt_insert->execute()) { // Menjalankan query insert
                // Setelah bukti tersimpan, update status pembelian menjadi "Diproses"
                $sql_update_status = "UPDATE pembelian SET id_status = (SELECT id_status FROM status WHERE status = 'Diproses') WHERE id_pembelian = ?"; // Query untuk mengupdate status
                $stmt_update_status = $conn->prepare($sql_update_status); // Menyiapkan statement
                $stmt_update_status->bind_param("i", $id_pembelian); // Mengikat parameter
                $stmt_update_status->execute(); // Menjalankan query update
        
                // Set success message in session after the form is submitted
                $_SESSION['alertMessage'] = "Bukti pembayaran berhasil diunggah dan status pembelian diperbarui menjadi Diproses!"; // Menyimpan pesan sukses ke sesi
                $_SESSION['alertType'] = "success"; // Tipe alert untuk sukses
        
                // Redirect to prevent form resubmission and show the alert
                header("Location: detail_pembayaran.php?id_pembelian=" . $id_pembelian); // Mengarahkan ke halaman detail pembayaran
                exit(); // Menghentikan eksekusi script
            } else {
                $alertMessage = "Terjadi kesalahan saat menyimpan data: " . $conn->error; // Pesan error jika gagal menyimpan
                $alertType = "danger"; // Tipe alert untuk error
                // Tambahkan log untuk debug
                echo "Database error: " . $conn->error; // Cek error dari database
            }
        } else {
            $alertMessage = "Error uploading file."; // Pesan error jika upload gagal
            $alertType = "danger"; // Tipe alert untuk error upload
        }        
    } else {
        $alertMessage = "Hanya file gambar yang diperbolehkan untuk diunggah."; // Pesan error jika tipe file tidak diperbolehkan
        $alertType = "danger"; // Tipe alert untuk error upload
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Menyertakan CSS Bootstrap -->
</head>
<body>

<div class="container mt-5"> <!-- Kontainer utama -->
    <h2>Halaman Pembayaran</h2>
    <?php if ($status == 'Belum Dibayar' && $metode_pembayaran !== 'COD'): ?> <!-- Cek status dan metode pembayaran -->
        <h4>Silakan unggah bukti pembayaran Anda:</h4>
        <form action="" method="post" enctype="multipart/form-data"> <!-- Form untuk mengunggah bukti pembayaran -->
            <input type="hidden" name="id_pembelian" value="<?php echo $id_pembelian; ?>"> <!-- Hidden field untuk ID pembelian -->
            <div class="mb-3">
                <label for="nama_bank" class="form-label">Nama Bank dan Nomor Rekening</label>
                <select class="form-select" id="nama_bank" name="nama_bank" required> <!-- Dropdown untuk memilih bank -->
                    <option value="">Pilih Rekening</option>
                    <?php while ($row = $bank_result->fetch_assoc()): ?> <!-- Mengambil data bank dari query -->
                        <option value="<?php echo $row['id_bank']; ?>">
                            <?php echo htmlspecialchars($row['rekening']) . ' (' . htmlspecialchars($row['nama_bank']) . ')'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <p>*Pilih salah satu rekening saja</p>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah Pembayaran</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo number_format($total_pembelian, 0, ',', '.'); ?>" readonly> <!-- Menampilkan jumlah pembayaran -->
            </div>
            <div class="mb-3">
                <label for="bukti" class="form-label">Bukti Pembayaran (Gambar)</label>
                <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*" required> <!-- Input file untuk bukti pembayaran -->
            </div>
            <button type="submit" class="btn btn-success">Kirim Bukti Pembayaran</button> <!-- Tombol submit -->
        </form>

    <?php elseif ($metode_pembayaran === 'COD'): ?> <!-- Jika metode pembayaran adalah COD -->
        <h4>Metode Pembayaran: COD</h4>
        <p>Silakan bayar kepada kurir saat barang diterima.</p> <!-- Pesan untuk COD -->
    <?php else: ?>
        <p>Pembayaran sudah dilakukan. Terima kasih!</p> <!-- Pesan jika pembayaran sudah dilakukan -->
    <?php endif; ?>
</div>

<?php include 'footer.php'; // Menyertakan footer halaman ?>
</body>
</html>

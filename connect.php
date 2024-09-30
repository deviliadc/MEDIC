<?php 
$servername = "localhost"; // Nama server database (localhost jika menggunakan server lokal)
$username = "root"; // Menggunakan 'root' sebagai username untuk akses database
$password = ""; // Jika tidak ada password untuk root, biarkan kosong
$dbname = "medic_db"; // Ganti dengan nama database Anda yang sebenarnya

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) { // Jika terjadi kesalahan saat menghubungkan
    die("Connection failed: " . $conn->connect_error); // Menampilkan pesan kesalahan dan menghentikan eksekusi skrip
}
?>

<?php 
session_start();
include '../connect.php';

// Ambil ID customer dari URL
if (isset($_GET['id_customer'])) {
    $id_customer = $_GET['id_customer'];

    // Hapus kategori dari database
    $sql = "DELETE FROM customer WHERE id_customer='$id_customer'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Pelanggan berhasil dihapus!'); window.location='pelanggan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $conn->error . "'); window.location='pelanggan.php';</script>";
    }
} else {
    echo "<script>alert('ID pelanggan tidak ditemukan.'); window.location='pelanggan.php';</script>";
}
?>

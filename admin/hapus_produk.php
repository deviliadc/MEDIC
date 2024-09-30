<?php
session_start();
include '../connect.php';

// Ambil ID produk dari URL
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Ambil informasi foto produk untuk dihapus dari server
    $sql = "SELECT foto_produk FROM produk WHERE id_produk = '$id_produk'";
    $result = $conn->query($sql);
    $produk = $result->fetch_assoc();

    // Hapus foto produk dari server
    if (file_exists($produk['foto_produk'])) {
        unlink($produk['foto_produk']);  // Menghapus file foto
    }

    // Hapus produk dari database
    $sql = "DELETE FROM produk WHERE id_produk = '$id_produk'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Produk berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus produk: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    header("Location: produk.php");
} else {
    header("Location: produk.php");
}

$conn->close();
?>

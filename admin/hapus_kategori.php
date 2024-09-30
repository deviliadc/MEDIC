<?php 
session_start();
include '../connect.php';

// Ambil ID kategori dari URL
if (isset($_GET['id_kategori'])) {
    $id_kategori = $_GET['id_kategori'];

    // Hapus kategori dari database
    $sql = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";
    if ($conn->query($sql) === TRUE) {
        header("Location: kategori.php?msg=delete_success");
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }
} else {
    header("Location: kategori.php");
}

$conn->close();
?>

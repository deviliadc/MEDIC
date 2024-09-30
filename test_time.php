<?php
session_start(); // Memulai sesi jika diperlukan
include 'connect.php'; // Menyertakan file koneksi

// Fungsi uniqueFileName untuk memastikan nama file tidak duplikat
function uniqueFileName($target_dir, $fileName) {
    $filePath = $target_dir . $fileName;
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Mendapatkan ekstensi file
    $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME); // Mendapatkan nama file tanpa ekstensi
    
    $counter = 1;
    while (file_exists($filePath)) {
        $newFileName = $fileBaseName . '-' . $counter . '.' . $fileExtension; // Tambahkan angka jika file sudah ada
        $filePath = $target_dir . $newFileName;
        $counter++;
    }
    
    return $filePath; // Mengembalikan nama file yang unik
}

$target_dir = "assets/bukti/"; // Direktori target upload
$fileName = "bukti-pembayaran.jpeg"; // Nama file contoh

// Mengukur waktu eksekusi fungsi uniqueFileName
$start_time = microtime(true); // Waktu mulai
$fileName = uniqueFileName($target_dir, $fileName); // Panggil fungsi yang diuji
$end_time = microtime(true); // Waktu selesai

$execution_time = $end_time - $start_time; // Menghitung waktu eksekusi
echo "Waktu eksekusi: " . $execution_time . " detik"; // Menampilkan hasil
echo "<br>Nama file yang dihasilkan: " . $fileName;
?>

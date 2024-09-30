<?php
session_start(); // Memulai sesi

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $_SESSION['username'] = ""; 
    session_unset(); 
    session_destroy(); 
    echo "<script>alert('Anda Telah Logout');</script>";
    header("Location: login.php"); 
    exit();
} else {
    echo "<script>
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '?confirm=yes';
        } else {
            window.location.href = 'index.php'; 
        }
    </script>";
}
?>

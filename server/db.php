<?php
// Konfigurasi database
$host = "localhost"; // Host database (default: localhost)
$username = "root";  // Username database (default: root untuk XAMPP)
$password = "";      // Password database (kosong untuk default XAMPP)
$dbname = "konseria";   // Nama database

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

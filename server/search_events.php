<?php
// Koneksi ke database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "konseria"; // Ganti dengan nama database Anda

// Ambil parameter pencarian dari query string
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape parameter pencarian untuk mencegah SQL injection
$searchTerm = $conn->real_escape_string($searchTerm);

// Ambil data event berdasarkan pencarian
$sql = "SELECT * FROM event WHERE title LIKE '%$searchTerm%' OR location LIKE '%$searchTerm%'"; // Ganti dengan nama tabel Anda
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

echo json_encode($events);
$conn->close();
?>
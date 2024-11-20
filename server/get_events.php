<?php
// Koneksi ke database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "konseria"; // Ganti dengan nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Array untuk bulan Indonesia
$bulanIndonesia = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

// Ambil data event
$sql = "SELECT * FROM event ORDER BY date ASC LIMIT 12"; // Ganti dengan nama tabel Anda
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format tanggal ke bahasa Inggris
        $tanggal = date('d F Y', strtotime($row['date']));

        // Ganti nama bulan ke bahasa Indonesia
        foreach ($bulanIndonesia as $en => $id) {
            $tanggal = str_replace($en, $id, $tanggal);
        }

        // Tambahkan tanggal yang sudah diformat ke data event
        $row['date_formatted'] = $tanggal;
        $events[] = $row;
    }
}

// Kembalikan data dalam format JSON
header('Content-Type: application/json'); // Pastikan respons adalah JSON
echo json_encode($events);
$conn->close();
?>

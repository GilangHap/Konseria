<?php
// add_event.php

// Mengatur header untuk menerima JSON
header('Content-Type: application/json');

// Menerima data JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data yang diterima
if (isset($data['title'], $data['description'], $data['date'], $data['time'], $data['location'], $data['imageURL'], $data['organizer'], $data['organizerImage'], $data['availableTickets'], $data['ticketPrice'])) {
    
    // Koneksi ke database
    $host = 'localhost';
    $dbname = 'konseria'; // Ganti dengan nama database Anda
    $username = 'root'; // Ganti dengan username database Anda
    $password = ''; // Ganti dengan password database Anda

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
        exit;
    }

    // Menyimpan data ke database
    try {
        $stmt = $pdo->prepare("INSERT INTO event (title, description, date, time, location, imageURL, organizer, organizerImage, availableTickets, ticketPrice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['date'],
            $data['time'],
            $data['location'],
            $data['imageURL'],
            $data['organizer'],
            $data['organizerImage'],
            $data['availableTickets'],
            $data['ticketPrice']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Event berhasil ditambahkan']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan event: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
}
?>
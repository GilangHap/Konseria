<?php
require_once 'db.php'; // Pastikan koneksi database sudah benar

// Cek apakah parameter ID ada
if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    // Query ke database
    $query = $conn->prepare("SELECT * FROM event WHERE id = ?");
    $query->bind_param("i", $eventId); // "i" menunjukkan tipe data integer
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();

        // Kirim data dalam format JSON
        echo json_encode([
            "success" => true,
            "event" => $event
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Event tidak ditemukan"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Parameter ID tidak ditemukan"
    ]);
}
?>

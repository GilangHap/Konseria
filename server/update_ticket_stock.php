<?php
header("Content-Type: application/json");

include 'db.php';

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['items'])) {
    foreach ($data['items'] as $item) {
        $eventId = $item['id'];
        $quantity = $item['quantity'];

        // Kurangi jumlah tiket yang tersedia
        $sql = "UPDATE event SET availableTickets = availableTickets - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $eventId);

        if (!$stmt->execute()) {
            echo json_encode(["error" => "Failed to update stock for event ID $eventId"]);
            exit;
        }
    }
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Invalid request data"]);
}

$conn->close();
?>

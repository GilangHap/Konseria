<?php
include 'db.php';

$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

$searchTerm = $conn->real_escape_string($searchTerm);

// Ambil data event berdasarkan pencarian
$sql = "SELECT * FROM event WHERE title LIKE '%$searchTerm%' OR location LIKE '%$searchTerm%'"; 
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
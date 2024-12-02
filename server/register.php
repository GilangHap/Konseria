<?php
header('Content-Type: application/json');

// Ambil input dari JavaScript
$input = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (!isset($input['name'], $input['email'], $input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

$name = $input['name'];
$email = $input['email'];
$password = password_hash($input['password'], PASSWORD_BCRYPT);

// Koneksi ke database
include 'db.php';
// Simpan data ke database
$stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $name, $email, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar.']);
}

$stmt->close();
$conn->close();
?>

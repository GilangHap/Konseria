<?php
require 'sendReceipt.php'; // Fungsi yang telah dibuat sebelumnya

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $email = $data['customer']['email'];
    $result = sendReceipt($email, $data);

    echo json_encode([
        "status" => "success",
        "message" => $result,
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Data pesanan tidak ditemukan",
    ]);
}
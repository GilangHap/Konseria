<?php
header('Content-Type: application/json');

// Ambil data dari frontend
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (!$data || !isset($data['customer']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

// Konfigurasi database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "konseria";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
    exit;
}

// Simpan data pesanan ke database
$orderId = $conn->real_escape_string($data['orderId']);
$fullName = $conn->real_escape_string($data['customer']['fullName']);
$email = $conn->real_escape_string($data['customer']['email']);
$whatsapp = $conn->real_escape_string($data['customer']['whatsapp']);
$total = $data['total'];

$query = "INSERT INTO orders (order_id, full_name, email, whatsapp, total) VALUES ('$orderId', '$fullName', '$email', '$whatsapp', $total)";
if (!$conn->query($query)) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesanan']);
    exit;
}

// Simpan item pesanan
foreach ($data['items'] as $item) {
    $itemId = $conn->real_escape_string($item['id']);
    $title = $conn->real_escape_string($item['title']);
    $price = $item['ticketPrice'];
    $quantity = $item['quantity'];

    $query = "INSERT INTO order_items (order_id, item_id, title, price, quantity) VALUES ('$orderId', '$itemId', '$title', $price, $quantity)";
    if (!$conn->query($query)) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan item pesanan']);
        exit;
    }
}

// Proses transaksi Midtrans
require_once 'Midtrans.php'; // Pastikan file SDK Midtrans sudah ada
\Midtrans\Config::$serverKey = 'SB-Mid-server-6V2F20Q6S2uqtCzAn8bxn23R';
\Midtrans\Config::$isProduction = false;

$params = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => $total,
    ],
    'customer_details' => [
        'first_name' => $fullName,
        'email' => $email,
        'phone' => $whatsapp,
    ],
    'item_details' => array_map(function ($item) {
        return [
            'id' => $item['id'],
            'price' => $item['ticketPrice'],
            'quantity' => $item['quantity'],
            'name' => $item['title'],
        ];
    }, $data['items']),
];

try {
    $snapToken = \Midtrans\Snap::createTransaction($params)->redirect_url;
    echo json_encode(['success' => true, 'paymentUrl' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

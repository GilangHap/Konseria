<?php
require_once '../vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Konfigurasi Midtrans
Config::$serverKey = 'SB-Mid-server-6V2F20Q6S2uqtCzAn8bxn23R';
Config::$isProduction = false; // Ubah ke true jika di produksi
Config::$isSanitized = true;
Config::$is3ds = true;

// Ambil data dari request
$requestPayload = file_get_contents('php://input');
$data = json_decode($requestPayload, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
    exit;
}

// Buat payload Snap
$order_id = $data['orderId'];
$gross_amount = $data['total'];

$item_details = array_map(function ($item) {
    return [
        'id' => $item['id'],
        'price' => $item['ticketPrice'],
        'quantity' => $item['quantity'],
        'name' => $item['title']
    ];
}, $data['items']);

$transactionDetails = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $gross_amount
    ],
    'item_details' => $item_details,
    'customer_details' => [
        'first_name' => explode(' ', $data['customer']['fullName'])[0],
        'last_name' => explode(' ', $data['customer']['fullName'])[1] ?? '',
        'email' => $data['customer']['email'],
        'phone' => $data['customer']['whatsapp']
    ]
];

try {
    $snapToken = Snap::getSnapToken($transactionDetails);
    echo json_encode(['success' => true, 'token' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

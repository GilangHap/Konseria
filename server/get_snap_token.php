<?php
require_once '../vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Konfigurasi Midtrans
Config::$serverKey = 'SB-Mid-server-6V2F20Q6S2uqtCzAn8bxn23R'; // Ganti dengan server key Anda
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

if (!isset($data['total'], $data['orderId'], $data['items'], $data['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
    exit;
}

// Log data untuk debugging
file_put_contents('log.txt', "Total dari Frontend: " . $data['total'] . PHP_EOL, FILE_APPEND);

// Tambahkan admin fee ke dalam total
$adminFee = 5000;
$gross_amount = $data['total'] + $adminFee; // Total termasuk admin fee

file_put_contents('log.txt', "Gross Amount yang dikirim: $gross_amount" . PHP_EOL, FILE_APPEND);

// Buat item details
$item_details = array_map(function ($item) {
    return [
        'id' => $item['id'],
        'price' => $item['ticketPrice'],
        'quantity' => $item['quantity'],
        'name' => $item['title']
    ];
}, $data['items']);

// Hitung total quantity semua item
$totalQuantity = array_reduce($data['items'], function ($carry, $item) {
    return $carry + $item['quantity'];
}, 0);

// Tambahkan admin fee sebagai item dengan quantity sesuai total item
$item_details[] = [
    'id' => 'admin_fee',
    'price' => $adminFee,
    'quantity' => $totalQuantity,
    'name' => 'Biaya Admin'
];

// Perbarui gross_amount agar sesuai dengan total item * admin fee
$gross_amount = $data['total'] + ($adminFee * $totalQuantity);

// Buat payload untuk Snap API
$transactionDetails = [
    'transaction_details' => [
        'order_id' => $data['orderId'],
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

// Log transaksi untuk debugging
file_put_contents('log.txt', "Transaction Details: " . print_r($transactionDetails, true) . PHP_EOL, FILE_APPEND);

try {
    // Dapatkan Snap Token dari Midtrans
    $snapToken = Snap::getSnapToken($transactionDetails);
    file_put_contents('log.txt', "Snap Token: $snapToken" . PHP_EOL, FILE_APPEND);

    echo json_encode(['success' => true, 'token' => $snapToken]);
} catch (Exception $e) {
    file_put_contents('log.txt', "Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

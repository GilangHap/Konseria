<?php
require_once 'midtrans_config.php';

$order_id = 'order-id-' . time();
$gross_amount = 10000;

$transaction_details = [
    'order_id' => $order_id,
    'gross_amount' => $gross_amount,
];

$item_details = [
    [
        'id' => 'item1',
        'price' => $gross_amount,
        'quantity' => 1,
        'name' => 'Nama Tiket'
    ]
];

$transaction = [
    'payment_type' => 'credit_card',
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
];

$snapToken = \Midtrans\Snap::getSnapToken($transaction);
echo json_encode(['token' => $snapToken]);
?>
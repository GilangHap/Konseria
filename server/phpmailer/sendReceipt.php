<?php
require '../../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReceipt($email, $orderData)
{
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'konseriaidn@gmail.com';
        $mail->Password   = 'nghk uqfu umnr lady';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Informasi Pengirim dan Penerima
        $mail->setFrom('konseriaidn@gmail.com', 'Konseria');
        $mail->addAddress($email);

        // Data dari pesanan
        $orderId = $orderData['orderId'];
        $customerName = $orderData['customer']['fullName'];
        $customerEmail = $orderData['customer']['email'];
        $items = $orderData['items'];

        // Hitung subtotal, biaya admin, dan total harga
        $subtotal = array_reduce($items, function ($total, $item) {
            return $total + $item['ticketPrice'] * $item['quantity'];
        }, 0);

        $totalQuantity = array_reduce($items, function ($total, $item) {
            return $total + $item['quantity'];
        }, 0);

        $adminFee = $totalQuantity * 5000;
        $totalPrice = $subtotal + $adminFee;

        // Membuat QR Code
        $qrCodeResult = Builder::create()
        ->writer(new PngWriter()) // Gunakan PngWriter
        ->data("Order ID: $orderId\nName: $customerName\nTotal: Rp" . number_format($totalPrice, 0, ',', '.'))
        ->size(150) // Atur ukuran QR Code (pixel)
        ->margin(5)
        ->build();

        // Tentukan path untuk menyimpan QR Code sementara
        $qrCodePath = __DIR__ . '/../../temp/temp_qr_code.png';

        // Simpan QR Code ke dalam file PNG
        if (file_put_contents($qrCodePath, $qrCodeResult->getString()) === false) {
            throw new Exception("Gagal menyimpan QR Code ke $qrCodePath");
        }

        // Membuat daftar tiket dalam HTML
        $itemsListHTML = '';
        foreach ($items as $item) {
            $itemTotal = $item['ticketPrice'] * $item['quantity'];
            $itemsListHTML .= "<li>{$item['title']} (x{$item['quantity']}) - Rp" . number_format($itemTotal, 0, ',', '.') . "</li>";
        }

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Struk Pembayaran Konseria';
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>
            <h1 style='text-align: center; color: #6C63FF;'>Terima Kasih!</h1>
            <p style='text-align: center;'>Pembayaran Anda telah berhasil.</p>
            <p style='text-align: center;'>Berikut detail pembelian Anda:</p>
            <div style='border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9;'>
                <h2 style='margin-bottom: 10px;'>Struk Pembelian</h2>
                <p><strong>ID Pesanan:</strong> $orderId</p>
                <p><strong>Nama:</strong> $customerName</p>
                <p><strong>Email:</strong> $customerEmail</p>
                <p><strong>Daftar Tiket:</strong></p>
                <ul style='padding-left: 20px;'>
                    $itemsListHTML
                </ul>
                <p><strong>Subtotal:</strong> Rp" . number_format($subtotal, 0, ',', '.') . "</p>
                <p><strong>Biaya Admin:</strong> Rp" . number_format($adminFee, 0, ',', '.') . "</p>
                <p><strong>Total Pembayaran:</strong> Rp" . number_format($totalPrice, 0, ',', '.') . "</p>
                <p><strong>QR Code Tiket Anda:</strong></p>
                <div style='text-align: center;'>
                    <img src='cid:qrcode' alt='QR Code' style='width: 150px; height: auto;'/>
                </div>
            </div>
            <p style='text-align: center; margin-top: 20px;'>Semoga Anda menikmati acara!</p>
        </div>
        ";

        // Lampirkan QR code ke email menggunakan inline image
        $mail->addEmbeddedImage($qrCodePath, 'qrcode');

        // Kirim email
        $mail->send();

        // Hapus file QR Code sementara setelah dikirim
        if (file_exists($qrCodePath)) {
            unlink($qrCodePath);
        }

        return ["status" => "success", "message" => "Email berhasil dikirim ke $email"];
    } catch (Exception $e) {
        return ["status" => "error", "message" => "Gagal mengirim email: {$mail->ErrorInfo}"];
    }
}
?>

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

        $itemsListText = '';
        foreach ($items as $item) {
            $itemsListText .= "{$item['title']} (x{$item['quantity']}), ";
        }
        $itemsListText = rtrim($itemsListText, ', '); // Hapus koma terakhir

        // Membuat QR Code
        $qrCodeResult = Builder::create()
        ->writer(new PngWriter()) 
        ->data("Order ID: $orderId\nName: $customerName\nItems: $itemsListText\nTotal: Rp" . number_format($totalPrice, 0, ',', '.'))
        ->size(150) 
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
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
        <div style='background-color: #133E87; padding: 20px; color: white; text-align: center;'>
            <h1 style='margin: 0; font-size: 24px;'>BUKTI TRANSAKSI</h1>
        </div>
        <div style='padding: 20px;'>
            <p>Halo <strong>$customerName</strong>,</p>
            <p>Transaksi kamu telah selesai kami proses dan bukti transaksinya terlampir pada email ini.</p>

            <h3 style='margin-bottom: 10px; font-size: 18px;'>Detail Transaksi</h3>
            <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                <tr>
                    <td style='padding: 5px 0;'>ID Pesanan</td>
                    <td style='padding: 5px 0; text-align: right;'>#{$orderId}</td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Nama</td>
                    <td style='padding: 5px 0; text-align: right;'>$customerName</td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Email</td>
                    <td style='padding: 5px 0; text-align: right;'>$customerEmail</td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Daftar Tiket</td>
                    <td style='padding: 5px 0; text-align: right;'>
                        <ul style='margin: 0; padding-left: 20px; text-align: left;'>$itemsListHTML</ul>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Subtotal</td>
                    <td style='padding: 5px 0; text-align: right;'>Rp" . number_format($subtotal, 0, ',', '.') . "</td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Biaya Admin</td>
                    <td style='padding: 5px 0; text-align: right;'>Rp" . number_format($adminFee, 0, ',', '.') . "</td>
                </tr>
                <tr>
                    <td style='padding: 5px 0;'>Total Pembayaran</td>
                    <td style='padding: 5px 0; text-align: right; font-weight: bold;'>Rp" . number_format($totalPrice, 0, ',', '.') . "</td>
                </tr>
            </table>

            <div style='margin-top: 20px; text-align: center;'>
                <p><strong>QR Code Tiket Anda:</strong></p>
                <img src='cid:qrcode' alt='QR Code' style='width: 150px; height: auto;'/>
            </div>
        </div>

        <div style='background-color: #133E87; padding: 10px; color: white; text-align: center;'>
            <p style='margin: 0; font-size: 12px;'>© 2024 Konseria. All rights reserved.</p>
        </div>
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

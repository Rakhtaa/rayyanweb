<?php
// Sertakan pustaka PHP QR Code
include 'phpqrcode/qrlib.php'; 

// Buat direktori sementara untuk menyimpan gambar QR code
$tempDir = "temp/";
if (!file_exists($tempDir)) {
    mkdir($tempDir); // Buat direktori jika belum ada
}

// Data yang ingin Anda ubah menjadi QR code
$data = 'form_scan.php'; // Ganti dengan teks atau URL yang Anda inginkan

// Nama file untuk menyimpan QR code
$fileName = $tempDir . 'qrcode.png';

// Buat QR code dan simpan sebagai file PNG
QRcode::png($data, $fileName, QR_ECLEVEL_L, 10); // QR_ECLEVEL_L untuk level error low, '10' adalah ukuran piksel

// Menampilkan QR code di halaman web
echo '<img src="'.$fileName.'" alt="QR Code">';

?>

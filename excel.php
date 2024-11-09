<?php
require 'vendor/autoload.php'; // Pastikan path ini benar sesuai direktori Anda

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use ZipArchive;

// Koneksi ke database
include('config.php');

// Array untuk menyimpan file Excel yang akan di-zip
$files = [];

// Ambil tanggal dan waktu sekarang untuk folder unik
$timestamp = date('Ymd_His');
$downloadDir = "C:/Users/Hp/Downloads/Export_$timestamp/"; // Folder baru berdasarkan waktu
if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0777, true); // Buat folder baru jika belum ada
}

// Cek apakah ada data riwayat kehadiran
$sqlCheckData = "SELECT COUNT(*) AS count FROM kehadiran";
$resultCheckData = $conn->query($sqlCheckData);
$rowCheckData = $resultCheckData->fetch_assoc();

if ($rowCheckData['count'] == 0) {
    // Jika tidak ada data, tampilkan alert dan hentikan proses
    echo "<script>alert('Tidak ada data kehadiran untuk diekspor.'); window.history.back();</script>";
    exit; // Hentikan script jika tidak ada data
}
// Ambil daftar kelas dari database
$sqlKelas = "SELECT DISTINCT kelas FROM kehadiran";
$resultKelas = $conn->query($sqlKelas);

if ($resultKelas === false) {
    die('Error: ' . $conn->error);
}

// Loop setiap kelas untuk membuat file Excel
while ($rowKelas = $resultKelas->fetch_assoc()) {
    $kelas = $rowKelas['kelas'];

    // Query untuk mendapatkan data kehadiran dari tiap kelas
    $sql = "SELECT * FROM kehadiran WHERE kelas = '$kelas'";
    $result = $conn->query($sql);

    if ($result === false) {
        die('Error: ' . $conn->error);
    }

    // Buat Spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header kolom
    $sheet->setCellValue('A1', 'Nama');
    $sheet->setCellValue('B1', 'Kelas');
    $sheet->setCellValue('C1', 'nis');
    $sheet->setCellValue('D1', 'Tanggal');
    $sheet->setCellValue('E1', 'Waktu');
    $sheet->setCellValue('F1', 'Status');

    // Menambahkan styling pada header
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

    // Mengatur auto-size untuk setiap kolom
    foreach (range('A', 'F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Isi data siswa ke dalam sheet
    $rowNumber = 2; // Mulai dari baris kedua
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['nama']);
        $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
        $sheet->setCellValue('C' . $rowNumber, $row['nis']);
        $sheet->setCellValue('D' . $rowNumber, $row['tanggal']);
        $sheet->setCellValue('E' . $rowNumber, $row['waktu']);
        $sheet->setCellValue('F' . $rowNumber, $row['status']);

        // Menambahkan border pada setiap sel untuk rapi
        $sheet->getStyle("A$rowNumber:F$rowNumber")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $rowNumber++;
    }

    // Simpan file Excel untuk kelas tersebut di direktori yang ditentukan
    $fileName = 'Data_Kehadiran_' . $kelas . '.xlsx';
    $filePath = $downloadDir . $fileName;
    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    // Tambahkan file ke array untuk di-zip
    $files[] = $filePath;
}

// Bersihkan buffer output sebelum menghasilkan file ZIP
if (ob_get_contents()) {
    ob_end_clean();
}

// Nama file ZIP
$zipFileName = "Data_Kehadiran_Semua_Kelas_$timestamp.zip";
$zipFilePath = $downloadDir . $zipFileName;

// Buat file ZIP
$zip = new ZipArchive();
if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    // Tambahkan setiap file ke dalam ZIP
    foreach ($files as $file) {
        if (file_exists($file)) {
            $zip->addFile($file, basename($file));
        } else {
            die('Error: File ' . $file . ' does not exist.');
        }
    }
    $zip->close();
} else {
    die('Failed to create ZIP file: ' . $zipFilePath);
}

// Periksa apakah file ZIP berhasil dibuat dan tidak kosong
if (filesize($zipFilePath) > 0) {
    // Header untuk mengunduh file ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
    header('Content-Length: ' . filesize($zipFilePath));

    // Baca file ZIP ke output untuk di-download
    readfile($zipFilePath);

    // Hapus file ZIP setelah di-download
    unlink($zipFilePath);

    // Hapus file Excel setelah di-zip
    foreach ($files as $file) {
        unlink($file);
    }

    // Jika semua berhasil, keluar
    exit;
} else {
    die('Error: ZIP file is empty or corrupted.');
}
?>

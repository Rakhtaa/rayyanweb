<?php
session_start();

include 'config.php';
if (!isset($_SESSION['nama'])) {
    $showAlert = true; 
} else {
    $showAlert = false; 
    $nama = $_SESSION['nama'];
}
$query = "SELECT nama, kelas, nis FROM user WHERE nama = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nama);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
$nama = $user['nama'] ?? ''; 
$kelas = $user['kelas'] ?? '';
$nis= $user['nis'] ?? ''; 
$stmt->close();

date_default_timezone_set('Asia/Makassar');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_SESSION['nama'];
    $kelas = htmlspecialchars(trim($_POST['kelas']));
    $nis = htmlspecialchars(trim($_POST['nis']));
    $tanggal = date('Y-m-d');
    $waktu = date('H:i');
    $timeObject = DateTime::createFromFormat('H:i', $waktu);
    $tepatWaktuStart = DateTime::createFromFormat('H:i', '06:00');
    $tepatWaktuEnd = DateTime::createFromFormat('H:i', '07:30');
    if ($timeObject >= $tepatWaktuStart && $timeObject <= $tepatWaktuEnd) {
        $status = 'TEPAT WAKTU';
    } else {
        $status = 'TERLAMBAT';
    }
   // Siapkan statement untuk memasukkan data ke kehadiran
   $stmt = $conn->prepare("INSERT INTO kehadiran (nama, kelas, nis, tanggal, waktu, status) VALUES (?, ?, ?, ?, ?, ?)");
   if ($stmt) { // Pastikan statement berhasil disiapkan
       $stmt->bind_param("ssssss", $nama, $kelas, $nis, $tanggal, $waktu, $status);
       if ($stmt->execute()) {
           $success_message = 'Kehadiran berhasil dicatat!';
       } else {
           $success_message = "Error: " . $stmt->error;
       }
       $stmt->close(); // Tutup statement setelah selesai digunakan
   } else {
       $success_message = "Error preparing statement: " . $conn->error;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleformscan.css">
    <script>
        const schoolLat = -5.1412992;
        const schoolLng = 119.488512;       
        const maxDistance = 1000; 
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; 
            const φ1 = lat1 * Math.PI/180; 
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2 - lat1) * Math.PI/180;
            const Δλ = (lon2 - lon1) * Math.PI/180;
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const distance = R * c; 
            return distance;
        }
        function checkLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const distance = calculateDistance(userLat, userLng, schoolLat, schoolLng);
                    const locationStatus = document.getElementById('locationStatus');
                    const submitBtn = document.getElementById('submitBtn');
                    if (distance <= maxDistance) {
                        locationStatus.innerHTML = 'Lokasi sesuai, Anda bisa mengisi kehadiran.';
                        locationStatus.classList.remove('alert-info');
                        locationStatus.classList.add('alert-success');
                        submitBtn.disabled = false; 
                    } else {
                        locationStatus.innerHTML = 'Lokasi Anda tidak sesuai, Anda tidak bisa mengisi kehadiran.';
                        locationStatus.classList.remove('alert-success');
                        locationStatus.classList.add('alert-danger');
                        submitBtn.disabled = true; 
                    }
                }, function() {
                    document.getElementById('locationStatus').innerHTML = 'Tidak bisa mendapatkan lokasi Anda.';
                });
            } else {
                document.getElementById('locationStatus').innerHTML = 'Geolocation tidak didukung oleh browser Anda.';
            }
        }
        window.onload = checkLocation;
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
    <img src="images/logoathirah.png" alt="Logo" style="height: 40px; margin-right: 10px;"> 
        <a class="navbar-brand" href="#"><b><i>Sistem Kehadiran Siswa</i></b></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="attendance.php">
                    <i class="bi bi-arrow-right"></i> Kembali</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <?php if ($showAlert): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            <strong>Perhatian!</strong> Anda harus login terlebih dahulu untuk mengakses halaman ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Script untuk redirect setelah alert ditutup -->
        <script>
            setTimeout(function() {
                window.location.href = 'login.php'; // Redirect ke halaman login setelah 3 detik
            }, 3000); // Ubah angka untuk mengatur waktu delay (dalam milidetik)
        </script>

<?php endif; ?>


<div class="container mt-5">
    <h2 class="text-center">DATA KEHADIRAN</h2>
    <div id="locationStatus" class="alert alert-info" role="alert">Memeriksa lokasi Anda...</div>
    <form action="" method="POST">
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
        setTimeout(function() {
            window.location.href = 'attendance.php'; // Redirect ke halaman attendance setelah 2 detik
        }, 2000); // Ubah angka untuk mengatur waktu delay (dalam milidetik)
        </script>
    <?php endif; ?>
        <div class="mb-3">
            <label for="nama" class="form-label"><b>Nama Lengkap</b></label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label"><b>Kelas</b></label>
            <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo htmlspecialchars($kelas); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="nis" class="form-label"><b>NIS</b></label>
            <input type="text" class="form-control" id="nis" name="nis" value="<?php echo htmlspecialchars($nis); ?>" readonly>
        </div>
        <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>Submit Kehadiran</button>
    </form>
</div>

<footer>
     Copyright 2024 By ©Rayyan Nakhlah Prayata.
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
session_start();
include('config.php'); 
if (!isset($_SESSION['nama'])) {
    header("Location: login.php");
    exit();
}
$nama = $_SESSION['nama'];
date_default_timezone_set('Asia/Makassar');
$sql_profile = "SELECT nama, kelas, nis FROM user WHERE nama = ?";
if ($stmt_profile = $conn->prepare($sql_profile)) {
    $stmt_profile->bind_param("s", $nama);
    $stmt_profile->execute();
    $stmt_profile->bind_result($nama, $kelas, $nis);
    $stmt_profile->fetch();
    $stmt_profile->close();
} else {
    echo "Error: " . htmlspecialchars($conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kehadiran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleattendance.css">
    
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
    <img src="images/logoathirah.png" alt="Logo" style="height: 40px; margin-right: 10px;"> 
        <a class="navbar-brand" href="#"><b><i>E</i>-ABSENSI</b></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
        aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">
                    <i class="bi bi-house-fill"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#scanqr">
                    <i class="bi bi-qr-code-scan"></i> Scan QR</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#riwayatKehadiran">
                    <i class="bi bi-calendar-check-fill"></i> Kehadiran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#riwayatKeterlambatan">
                    <i class="bi bi-calendar-x-fill"></i> Keterlambatan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                    <i class="bi bi-door-open-fill"></i> Log-out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container mt-5 content">
    <h2>Selamat datang, <?php echo htmlspecialchars($nama); ?>!</h2>
<div class="row mb-4">
    <div class="profile-info">
        <h4 id="profilesiswa" class="bi bi-person-fill"> Profil Siswa</h4>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($nama); ?></p>
        <p><strong>Kelas:</strong> <?php echo htmlspecialchars($kelas); ?></p>
        <p><strong>NIS:</strong> <?php echo htmlspecialchars($nis); ?></p>
    </div>
</div>
<!-- Scan QR Code Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <h4 class="bi bi-qr-code-scan"> Scan QR Code</h4>
        <a href="scan.php" class="btn btn-outline-success" 
        id="scanqr"> Scan Kehadiran Kamu</a>
    </div>
</div>

<!-- Attendance History Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <h4 id="riwayatKehadiran" class="bi bi-calendar-check-fill"> Riwayat Kehadiran</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Datang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                    <?php
                    $sql = "SELECT *, DATE_FORMAT(tanggal, '%d-%m-%Y') 
                    as formatted_date, TIME_FORMAT(waktu, '%H:%i') as formatted_time 
                            FROM kehadiran 
                            WHERE nama = ? 
                            ORDER BY tanggal DESC, waktu DESC";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("s", $nama);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_class = ($row['status'] == 'Tepat Waktu') ? 
                                'status-hijau' : 'status-merah';
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['formatted_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['formatted_time']) . "</td>";
                                echo "<td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada riwayat kedatangan.</td></tr>";
                        }
                        $stmt->close();
                    } else {
                        echo "<tr><td colspan='3'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Late Attendance History Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <h4 id="riwayatKeterlambatan" class="bi bi-calendar-x-fill"> Riwayat Keterlambatan</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Datang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                    <?php
                    $sql_late = "SELECT *, DATE_FORMAT(tanggal, '%d-%m-%Y') as 
                    formatted_date, TIME_FORMAT(waktu, '%H:%i') as formatted_time 
                    FROM kehadiran 
                    WHERE nama = ? AND status = 'Terlambat' 
                    ORDER BY tanggal DESC, waktu DESC";
                    if ($stmt_late = $conn->prepare($sql_late)) {
                        $stmt_late->bind_param("s", $nama);
                        $stmt_late->execute();
                        $result_late = $stmt_late->get_result();
                        if ($result_late->num_rows > 0) {
                            while ($row_late = $result_late->fetch_assoc()) {
                                $status_class = 'status-merah';
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row_late['formatted_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row_late['formatted_time']) . "</td>";
                                echo "<td class='$status_class'>" . htmlspecialchars($row_late['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada riwayat keterlambatan.</td></tr>";
                        }
                        $stmt_late->close();
                    } else {
                        echo "<tr><td colspan='3'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
     Copyright 2024 By ©Rayyan Nakhlah Prayata.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

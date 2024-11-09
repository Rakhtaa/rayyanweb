<?php
include('config.php'); 
$query = "SELECT DATE(tanggal) AS tanggal, 
                 COUNT(*) AS jumlah_kehadiran, 
                 IFNULL(SUM(CASE WHEN status = 'Tepat Waktu' THEN 1 ELSE 0 END), 0) AS tepat_waktu, 
                 IFNULL(SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END), 0) AS terlambat
          FROM kehadiran
          WHERE DATE(tanggal) = CURDATE()
          GROUP BY DATE(tanggal)";

$result = $conn->query($query);

// Menyiapkan data untuk grafik
$tanggal = [];
$jumlahKehadiran = [];
$tepatWaktu = [];
$terlambat = [];

while ($row = $result->fetch_assoc()) {
    $tanggal[] = $row['tanggal'];
    $jumlahKehadiran[] = $row['jumlah_kehadiran'];
    $tepatWaktu[] = $row['tepat_waktu'];
    $terlambat[] = $row['terlambat'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - E-Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylehome.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Menambahkan Chart.js -->
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <img src="images/logoathirah.png" alt="Logo" style="height: 40px; margin-right: 10px;"> 
            <a class="navbar-brand" href="#"><b><i>E</i>-ABSENSI</b></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation"> 
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#absen">
                        <i class="bi bi-qr-code-scan">
                    </i> Absen</a></li>
                    <li class="nav-item"><a class="nav-link" href="#absen">
                        <i class="bi bi-person-walking">
                    </i> Tutorial Penggunaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#grafik">
                        <i class="bi bi-bar-chart-fill">
                    </i> Grafik Kehadiran</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">
                        <i class="bi bi-send-fill">
                    </i> Tentang</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container content">
        <!-- Bagian Quotes dan Gambar -->
        <div class="row row-quotes">
            <div class="col-md-7">
                <p class="quote">"Disiplin adalah kunci kesuksesan. Datang tepat waktu adalah langkah pertama untuk mencapai cita-cita."</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="images/siswa.png" alt="Logo Siswa" class="student-image">
            </div>
        </div>
    
        <!-- About Section -->
        <div class="about-section mt-5" id="tentang">
        <video autoplay muted loop playsinline>
        <source src="/PROJEK2/images/athirah.mp4" type="video/mp4">
        Browser Anda tidak mendukung video.
        </video>

            <h3>Tentang <i>E</i>-Absensi</h3>
            <p>Selamat datang di sistem E-Absensi SMA Islam Athirah Bukit Baruga. Website ini dirancang untuk mempermudah proses absensi siswa secara digital menggunakan QR Code. Dengan sistem ini, siswa dapat mencatat kehadiran dengan cepat dan akurat, serta membantu pihak sekolah memonitor kedisiplinan siswa dalam hal kehadiran.</p>
            <br>
            <p>Tujuan dari sistem ini adalah untuk mendukung kedisiplinan dan memberikan kemudahan bagi siswa dan guru dalam mencatat data kehadiran. Teruslah disiplin dan datang tepat waktu demi masa depan yang lebih baik!</p>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <a href="attendance.php" class="btn btn-outline-success btn-absensi" id="absen">Mulai Absensi</a>
        </div>
        
        <div class="container mt-5 c-tutor">
    <h5 class="text-center">Cara Penggunaan E-Absensi</h5>
    <ol class="text-start">
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/scan.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Buka halaman absensi dengan mengklik tombol <strong>Mulai Absensi</strong></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/login.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Kemudian <i>login</i> menggunakan <b>Nama Lengkap</b> dan <b>NIS</b></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/attendance.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Setelah berhasil <i>login</i>, maka akan masuk pada halaman <i>attendance</i></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/tombolscan.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Klik tombol <i>Scan QR</i> untuk melakukan absensi <i>scan barcode</i></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/scan.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Kemudian ketika kamera sudah terbuka, maka arahkan kamera ke <i>barcode</i> yang disediakan</p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/submit.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Lalu akan diarahkan ke halaman <i>submit</i></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/salah.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Jika lokasi siswa tidak sesuai, maka akan muncul pesan <b>lokasi tidak sesuai</b></p></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/benar1.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Jika lokasi siswa sesuai, maka akan muncul pesan <b>lokasi sesuai</b></p>
        </li>
        <li class="d-flex flex-column flex-md-row align-items-center mb-3">
            <img src="images/success.png" alt="Langkah 1" style="width: 50px; height: auto;" class="mb-2 mb-md-0 zoomable" onclick="zoomImage(this.src)">
            <p class="text-left text-md-start ms-md-3">Setelah berhasil <i>submit</i>, maka akan kembali di halaman <i>attendance</i></p>
        </li>
    </ol>
</div>

<!-- Elemen untuk gambar yang diperbesar -->
<div class="zoomed-image" id="zoomedImage" onclick="closeZoom()">
    <img id="zoomedImg" src="" alt="Zoomed Image">
</div>


<div class="container mt-5">
    <h3 id="grafik" class="text-center">Grafik Kehadiran Siswa</h3>
    <canvas id="attendanceChart"></canvas>
    <div class="chart-caption mt-3">
        <p id="totalKehadiran">Jumlah Kehadiran: <span></span></p>
        <p id="totalTepatWaktu">Tepat Waktu: <span></span></p>
        <p id="totalTerlambat">Terlambat: <span></span></p>
    </div>
</div>
    <!-- Footer -->
    <footer>
        Copyright 2024 By ©Rayyan Nakhlah Prayata.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mengambil data dari PHP ke JavaScript
        const tanggal = <?php echo json_encode($tanggal); ?>;
        const jumlahKehadiran = <?php echo json_encode($jumlahKehadiran); ?>;
        const tepatWaktu = <?php echo json_encode($tepatWaktu); ?>;
        const terlambat = <?php echo json_encode($terlambat); ?>;

        // Debugging untuk memastikan data diterima dengan benar
        console.log("Tanggal:", tanggal);
        console.log("Jumlah Kehadiran:", jumlahKehadiran);
        console.log("Tepat Waktu:", tepatWaktu);
        console.log("Terlambat:", terlambat);

        const totalKehadiran = jumlahKehadiran.reduce((acc, curr) => acc + curr, 0);
    const totalTepatWaktu = tepatWaktu.reduce((acc, curr) => acc + curr, 0);
    const totalTerlambat = terlambat.reduce((acc, curr) => acc + curr, 0);

    // Update nilai caption dengan total
    document.getElementById('totalKehadiran').querySelector('span').innerText = totalKehadiran;
    document.getElementById('totalTepatWaktu').querySelector('span').innerText = totalTepatWaktu;
    document.getElementById('totalTerlambat').querySelector('span').innerText = totalTerlambat;

        const ctx = document.getElementById('attendanceChart').getContext('2d');

        // Membuat grafik Chart.js
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: tanggal,
                datasets: [
                    {
                        label: 'Jumlah Kehadiran',
                        data: jumlahKehadiran,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Tepat Waktu',
                        data: tepatWaktu,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 255, 0, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Terlambat',
                        data: terlambat,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Kehadiran' }
                    },
                    x: {
                        title: { display: true, text: 'Tanggal' }
                    }
                }
            }
        });
    </script>
   <script>
function zoomImage(src) {
    const zoomedImage = document.getElementById('zoomedImage');
    const zoomedImg = document.getElementById('zoomedImg');
    
    zoomedImg.src = src; // Mengatur sumber gambar zoomed
    zoomedImage.style.display = 'flex'; // Menampilkan elemen zoomed
}

function closeZoom() {
    const zoomedImage = document.getElementById('zoomedImage');
    zoomedImage.style.display = 'none'; // Menyembunyikan elemen zoomed
}
</script>
</body>
</html>

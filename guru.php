<?php
session_start();
include('config.php');

// Ambil data siswa dari tabel 'kehadiran'
$data_siswa = [];

$sql = "SELECT nama, kelas, nis, 
        DATE(tanggal) as formatted_date, 
        TIME(waktu) as formatted_time, 
        status 
        FROM kehadiran";

$result = $conn->query($sql);

// Set timezone ke WITA
date_default_timezone_set('Asia/Makassar');

if (!$result) {
    die("Query error: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Konversi tanggal dan waktu untuk memastikan format benar
        $date = date("d-m-Y", strtotime($row['formatted_date']));
        $time = date("H:i", strtotime($row['formatted_time']));

        // Tentukan status berdasarkan waktu kedatangan
        $timeObject = DateTime::createFromFormat('H:i', $time);
        $threshold = DateTime::createFromFormat('H:i', '07:30');
        
        if ($timeObject > $threshold) {
            $row['status'] = 'TERLAMBAT';
        } else {
            $row['status'] = 'TEPAT WAKTU';
        }

        // Masukkan data ke array
        $data_siswa[] = [
            'nama' => $row['nama'],
            'kelas' => $row['kelas'],
            'nim' => $row['nis'],
            'formatted_date' => $date, // Tanggal yang sudah diformat
            'formatted_time' => $time, // Waktu yang sudah diformat
            'status' => $row['status']
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Kehadiran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleguru.css">
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
                    <a class="nav-link" href="https://www.smaathirahbaruga.sch.id">
                    <i class="bi bi-house-fill"></i> Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-center" style="flex: 1;">Riwayat Kehadiran Siswa</h2>
    </div>
    <div class="row mt-3">
        <div class="col-md-XII">
            <input id="searchInput" type="text" 
            class="form-control search-input" placeholder="Cari Nama Siswa...">
        </div>
    </div>

    <!-- Dropdown untuk memilih kelas dan tombol ekspor Excel -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" 
                id="kelasDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Kelas</button>
                <ul class="dropdown-menu" aria-labelledby="kelasDropdown">
                <li><a class="dropdown-item" href="#" data-kelas="">Semua Kelas</a></li>
                    <!-- Kelas X -->
                    <li><a class="dropdown-item" href="#" data-kelas="X.1">Kelas X.1</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="X.2">Kelas X.2</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="X.3">Kelas X.3</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="X.4">Kelas X.4</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="X.5">Kelas X.5</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="X.6">Kelas X.6</a></li>
                    <!-- Kelas XI -->
                    <li><a class="dropdown-item" href="#" data-kelas="XI.1">Kelas XI.1</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XI.2">Kelas XI.2</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XI.3">Kelas XI.3</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XI.4">Kelas XI.4</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XI.5">Kelas XI.5</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XI.6">Kelas XI.6</a></li>
                    <!-- Kelas XII -->
                    <li><a class="dropdown-item" href="#" data-kelas="XII.1">Kelas XII.1</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XII.2">Kelas XII.2</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XII.3">Kelas XII.3</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XII.4">Kelas XII.4</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XII.5">Kelas XII.5</a></li>
                    <li><a class="dropdown-item" href="#" data-kelas="XII.6">Kelas XII.6</a></li>
                </ul>
            </div>
        </div>
<div class="col-md-4 text-end">
    <form action="excel.php" method="post" class="d-inline-block">
        <input type="hidden" name="kelas" id="selectedKelas" value="">
        <button type="submit" class="btn btn-outline-success"> Ekspor ke Excel</button>
    </form>
</div>
</div>

    <!-- Tabel data kehadiran siswa dengan pagination -->
<table id="attendanceTable" class="table table-striped mt-4">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kelas</th>
            <th>NIS</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
        </tr>
    </thead>
        <tbody>
            <?php
            if (!empty($data_siswa)) {
                foreach ($data_siswa as $siswa) {
                    $status_class = ($siswa['status'] == 'TEPAT WAKTU') ? 'status-hijau' : 'status-merah';
                    echo "<tr data-kelas='{$siswa['kelas']}'>";
                    echo "<td>{$siswa['nama']}</td>";
                    echo "<td>{$siswa['kelas']}</td>";
                    echo "<td>{$siswa['nim']}</td>";
                    echo "<td>{$siswa['formatted_date']}</td>";
                    echo "<td>{$siswa['formatted_time']}</td>";
                    echo "<td class='{$status_class}'>{$siswa['status']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada data kehadiran.</td></tr>";
            }
            ?>
    </tbody>
</table>

    <!-- Navigasi pagination -->
    <nav>
    <ul class="pagination justify-content-end">
        <li class="page-item">
            <a class="page-link" href="#" id="prevPage">
                <i class="bi bi-arrow-left-circle"></i>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="#" id="nextPage">
                <i class="bi bi-arrow-right-circle"></i>
            </a>
        </li>
    </ul>
</nav>
</div>
<footer>
     Copyright 2024 By ©Rayyan Nakhlah Prayata.
</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        const rowsPerPage = 15; // Jumlah baris per halaman
        let currentPage = 1; // Halaman saat ini
        const rows = $("#attendanceTable tbody tr");
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        // Fungsi untuk menampilkan baris sesuai halaman
        function showPage(page) {
            rows.hide(); // Sembunyikan semua baris
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.slice(start, end).show(); // Tampilkan baris sesuai halaman

            // Sesuaikan navigasi
            $("#prevPage").parent().toggleClass("disabled", page === 1);
            $("#nextPage").parent().toggleClass("disabled", page === totalPages);
        }

        // Navigasi ke halaman berikutnya
        $("#nextPage").on("click", function (e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Navigasi ke halaman sebelumnya
        $("#prevPage").on("click", function (e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        // Pencarian siswa
        $("#searchInput").on("keyup", function () {
            const value = $(this).val().toLowerCase();
            rows.filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Dropdown kelas
        $(".dropdown-item").on("click", function () {
            var kelas = $(this).data("kelas");
            $("#selectedKelas").val(kelas);
            $("#kelasDropdown").text($(this).text());
            $("#attendanceTable tbody tr").show(); // Tampilkan semua baris

            if (kelas) {
                $("#attendanceTable tbody tr").not(`[data-kelas='${kelas}']`).hide(); // Sembunyikan yang tidak sesuai kelas
            }
        });
    });
        // Tampilkan halaman pertama saat memuat
        showPage(currentPage);
</script>
</body>
</html>
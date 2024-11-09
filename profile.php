<?php
session_start();
include 'config.php'; // Koneksi ke database

// Mendapatkan data dari session
$nama = $_SESSION['nama'];

// Mendapatkan data siswa dari database berdasarkan username
$sql = "SELECT * FROM user WHERE nama = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $nama);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data yang diinputkan dari form
    $new_nama = $_POST['nama'];
    $new_nis = $_POST['nis'];

    // Update data di tabel users
    $update_sql = "UPDATE user SET nama = ?, nis = ? WHERE nama = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('sss', $new_nama, $new_nis, $nama);

    if ($stmt->execute()) {
        // Update juga username di session
        $_SESSION['nama'] = $new_nama;

        // Update username di tabel kehadiran
        $update_kehadiran_sql = "UPDATE kehadiran SET nama = ?, nis = ? WHERE nama = ?";
        $stmt = $conn->prepare($update_kehadiran_sql);
        $stmt->bind_param('sss', $new_nama, $new_nis, $nama);
        $stmt->execute();

        echo "<p style='color: green;'>Profil berhasil diperbarui!</p>";
        header('Location: profile.php'); // Refresh halaman
        exit();
    } else {
        echo "<p style='color: red;'>Gagal memperbarui profil!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styleprofile.css">
    
</head>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <img src="images/logoathirah.png" alt="Logo" style="height: 40px; margin-right: 10px;">
        <a class="navbar-brand" href="#"><b><i>Sistem Kehadiran Siswa</i></b></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="attendance.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" 
                    height="16" fill="currentColor" class="bi bi-arrow-return-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l
                    -3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L1
                    3.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5"/>
                    </svg> Kembali</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content">
    <h2 class="text-center">PROFIL SISWA</h2>
    <!-- Tampilkan profil siswa -->
    <div class="mb-4">
        <p><strong>Nama Lengkap : </strong> <?php echo $user['nama']; ?></p>
        <p><strong>Kelas : </strong> <?php echo $user['kelas']; ?></p>
        <p><strong>NIS : </strong> <?php echo $user['nis']; ?></p>
    </div>

    <h3 class="text-center">Edit Akun</h3>

    <form method="POST" action="profile.php">
        <div class="form-group mb-3">
            <label for="nama">NAMA</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $user['nama']; ?>" 
            required>
        </div>
        <div class="form-group mb-3 password-container">
            <label for="nis">nis</label>
            <input type="password" class="form-control" id="nis" name="nis" value="<?php echo $user['nis']; ?>" 
            required>
            <span class="toggle-password" onclick="togglenisVisibility()">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </span>
        </div>
        <button type="submit" class="btn btn-outline-success">Update</button>
    </form>
</div>

<footer class="footer">
    <p>Copyright 2024 By ©Rayyan Nakhlah Prayata.</p>
</footer>

<script>
function togglenisVisibility() {
    var nisField = document.getElementById("nis");
    var eyeIcon = document.getElementById("eyeIcon");
    if (nisField.type === "password") {
        nisField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        nisField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

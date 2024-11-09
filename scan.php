<?php
if (isset($_POST['qrdata'])) {
    $qrData = $_POST['qrdata'];
    $safeQrData = htmlspecialchars($qrData);
    echo "Data QR: $safeQrData <br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylescan.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <img src="images/logoathirah.png" alt="Logo" style="height: 40px; margin-right: 10px;">
    <a class="navbar-brand" href="#"><b><i>Sistem Kehadiran Siswa</i></b></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
    data-bs-target="#navbarNav" aria-controls="navbarNav" 
    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="attendance.php">
            <i class="bi bi-arrow-right"></i> Kembali
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h1>Scan QR Code</h1>
    <div class="card p-3">
        <div class="row">
                <video id="preview"></video>
            <div class="col-md-4 d-flex flex-column justify-content-center align-items-center">
                <form method="post" id="qrForm">
                    <input type="hidden" name="qrdata" id="qrdata">
                </form>
            </div> 
        </div>
    </div>
</div>
<footer>
     Copyright 2024 By ©Rayyan Nakhlah Prayata.
</footer>

    <!-- Jika QR code ditemukan, lakukan redirect -->
<?php if (isset($safeQrData)): ?>
    <script type="text/javascript">
        window.location.href = "<?php echo $safeQrData; ?>";
    </script>
<?php endif; ?>
    
    <!-- Instascan JS Library -->
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
<script type="text/javascript">
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    scanner.addListener('scan', function (content) {
        document.getElementById('qrdata').value = content;
        document.getElementById('qrForm').submit(); 
    });
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            var selectedCamera = null;
            cameras.forEach(function(camera) {
                if (camera.name.indexOf('back') !== -1 || camera.name.indexOf('environment') !== -1) {
                    selectedCamera = camera;
                }
            });
            if (selectedCamera) {
                scanner.start(selectedCamera);
            } else {
                scanner.start(cameras[0]); 
            }
            } else {
                console.error('Tidak ada kamera yang ditemukan.');
            }
    }).catch(function (e) {
    console.error(e);
    });
</script>

</body>
</html>  

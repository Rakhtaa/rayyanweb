<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Lokasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    // Fungsi untuk mendapatkan lokasi pengguna secara terus-menerus dengan akurasi tinggi
    function getLocation() {
        // Periksa apakah browser mendukung Geolocation API
        if (navigator.geolocation) {
            // Menggunakan watchPosition untuk update lokasi secara berkelanjutan
            navigator.geolocation.watchPosition(showPosition, showError, { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 });
        } else {
            document.getElementById("location").innerHTML = "Geolocation tidak didukung oleh browser Anda.";
        }
    }

    // Fungsi untuk menampilkan posisi (latitude dan longitude)
    function showPosition(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var accuracy = position.coords.accuracy; // Akurasi dalam meter
        document.getElementById("location").innerHTML = 
            "Latitude: " + latitude + "<br>Longitude: " + longitude + "<br>Akurasi: " + accuracy + " meter";
    }
F
    // Fungsi untuk menangani kesalahan geolocation
    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                document.getElementById("location").innerHTML = "Pengguna menolak permintaan geolocation.";
                break;
            case error.POSITION_UNAVAILABLE:
                document.getElementById("location").innerHTML = "Informasi lokasi tidak tersedia.";
                break;
            case error.TIMEOUT:
                document.getElementById("location").innerHTML = "Permintaan lokasi pengguna melampaui batas waktu.";
                break;
            case error.UNKNOWN_ERROR:
                document.getElementById("location").innerHTML = "Terjadi kesalahan yang tidak diketahui.";
                break;
        }
    }
</script>

</head>
<body>
<div class="container mt-5">
    <h2>Cek Lokasi Device</h2>
    <p>Klik tombol di bawah untuk mengetahui lokasi device Anda.</p>
    <button onclick="getLocation()" class="btn btn-primary">Dapatkan Lokasi</button>
    <div id="location" class="mt-3 alert alert-info">Lokasi Anda akan muncul di sini.</div>
</div>
</body>
</html>

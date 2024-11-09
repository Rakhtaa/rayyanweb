<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Accurate Location</title>
</head>
<body>
    <h1>Get Accurate Location</h1>
    <button onclick="getLocation()">Get Location</button>
    <p id="location"></p>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                const options = {
                    enableHighAccuracy: true, // Minta lokasi dengan akurasi tinggi
                    timeout: 10000,           // Waktu tunggu sebelum timeout
                    maximumAge: 0             // Tidak menggunakan lokasi yang sudah ada
                };

                navigator.geolocation.getCurrentPosition(showPosition, showError, options);
            } else {
                document.getElementById("location").innerHTML = "Geolocation tidak didukung oleh browser Anda.";
            }
        }

        function showPosition(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy; // Akurasi dalam meter

            // Tampilkan informasi lokasi dan akurasi
            document.getElementById("location").innerHTML = 
                "Latitude: " + latitude + "<br>" +
                "Longitude: " + longitude + "<br>" +
                "Akurasi: " + accuracy + " meter" + "<br>" +
                (accuracy > 50 ? "<strong>Perhatian:</strong> Akurasi lokasi rendah, coba di tempat terbuka." : "");
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById("location").innerHTML = "Pengguna menolak permintaan untuk mendapatkan lokasi.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById("location").innerHTML = "Lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    document.getElementById("location").innerHTML = "Permintaan untuk mendapatkan lokasi melebihi waktu tunggu.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById("location").innerHTML = "Terjadi kesalahan yang tidak diketahui.";
                    break;
            }
        }
    </script>
</body>
</html>
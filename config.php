<?php
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "DATARAY"; 

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

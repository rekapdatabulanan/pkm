<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_absensi");
if (!$koneksi) die("Koneksi gagal: " . mysqli_connect_error());
session_start();
?>

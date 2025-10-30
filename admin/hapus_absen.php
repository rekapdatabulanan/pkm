<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");
mysqli_query($koneksi, "DELETE FROM absen");
header("Location: dashboard.php");
?>

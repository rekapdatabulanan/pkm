<?php
include 'config.php';

// Jika belum login, arahkan ke halaman login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Jika sudah login, arahkan sesuai role
if ($_SESSION['user']['role'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit;
} else {
    header("Location: user/dashboard.php");
    exit;
}
?>

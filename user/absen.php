<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'user') header("Location: ../login.php");

// Set timezone WITA
date_default_timezone_set('Asia/Makassar'); // WITA

$user_id = $_SESSION['user']['id'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Ambil pengaturan
$set = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
$hari_kerja = explode(",", str_replace(" ", "", $set['hari_kerja']));

// Ambil daftar hari libur
$libur_arr = [];
$libur_q = mysqli_query($koneksi, "SELECT tanggal FROM hari_libur");
while($row = mysqli_fetch_assoc($libur_q)) {
    $libur_arr[] = $row['tanggal'];
}

// Cek apakah hari ini bisa absen
$nama_hari = date('l', strtotime($tanggal));
$boleh_absen = in_array($nama_hari, $hari_kerja) && !in_array($tanggal, $libur_arr);

if (isset($_POST['absen'])) {
    if (!$boleh_absen) {
        $_SESSION['alert'] = ['type' => 'blue', 'msg' => 'Hari ini libur, Anda tidak bisa absen!'];
    } else {
        $lat = $_POST['latitude'];
        $lon = $_POST['longitude'];

        // Cek apakah user sudah absen hari ini
        $cek = mysqli_query($koneksi, "SELECT * FROM absen WHERE user_id='$user_id' AND tanggal='$tanggal'");
        if (mysqli_num_rows($cek) > 0) {
            $_SESSION['alert'] = ['type' => 'red', 'msg' => 'Anda sudah absen hari ini!'];
        } else {
            mysqli_query($koneksi, "INSERT INTO absen VALUES('', '$user_id', '$tanggal', '$jam', '$lat', '$lon')");
            $_SESSION['alert'] = ['type' => 'green', 'msg' => 'Absen berhasil!'];
        }
    }
    // Redirect agar tidak muncul Confirm Form Resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Absen Lokasi</title>
<style>
/* Global */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;

    /* Background image */
    background: url('../img/puskes.jpg') no-repeat center center fixed;
    background-size: cover;

    /* Overlay gelap agar teks terbaca */
    position: relative;
}

body::before {
    content: '';
    position: absolute;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.5);
    z-index: 0;
}

/* Konten utama agar berada di atas overlay */
.content {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 500px;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h2 {
    color: #fff;
    margin: 0;
}

.logout-btn {
    padding: 10px 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #dc3545, #b02a37);
    color: white;
    cursor: pointer;
    transition: 0.3s;
}
.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}

/* Alert */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    width: 100%;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Info libur */
.libur-msg {
    background: rgba(0,123,255,0.8);
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    text-align: center;
}

/* Form Absen */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

button[name="absen"] {
    padding: 15px 25px;
    font-size: 18px;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #007bff, #0056d6);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    width: 100%;
}
button[name="absen"]:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}
button[name="absen"]:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Responsive */
@media screen and (max-width: 480px) {
    button[name="absen"], .logout-btn {
        width: 90%;
        font-size: 16px;
    }
    .alert, .libur-msg {
        width: 90%;
    }
}
</style>
</head>
<body>

<div class="content">
    <div class="header">
        <h2>Absen Lokasi</h2>
    </div>

    <?php if (isset($_SESSION['alert'])): ?>
    <div class="alert" style="background:
        <?php 
            switch($_SESSION['alert']['type']){
                case 'red': echo '#dc3545'; break;
                case 'green': echo '#28a745'; break;
                case 'blue': echo '#007bff'; break;
            }
        ?>;">
        <?= htmlspecialchars($_SESSION['alert']['msg']); ?>
    </div>
    <?php unset($_SESSION['alert']); endif; ?>

    <?php if (!$boleh_absen): ?>
        <div class="libur-msg">Hari ini libur, Anda tidak bisa absen.</div>
    <?php endif; ?>

    <form method="post" id="formAbsen">
        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="lon">
        <button name="absen" <?= !$boleh_absen ? 'disabled' : '' ?>>Absen Sekarang</button>
    </form>
    <form action="../logout.php" method="post">
            <button type="submit" class="logout-btn">🚪 Keluar</button>
        </form>
</div>

<script>
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
        document.getElementById('lat').value = pos.coords.latitude;
        document.getElementById('lon').value = pos.coords.longitude;
    }, err => {
        console.warn('Geolocation tidak diizinkan:', err);
    });
} else {
    console.warn('Browser tidak mendukung Geolocation');
}
</script>

</body>
</html>



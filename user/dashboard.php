<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'user') header("Location: ../login.php");

$id_user = $_SESSION['user']['id'];

// Ambil pengaturan jam masuk
$set = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
$jam_masuk = $set['jam_masuk'];

// Ambil 10 absensi terakhir user
$absen = mysqli_query($koneksi, "
    SELECT * FROM absen 
    WHERE user_id='$id_user' 
    ORDER BY tanggal DESC, jam DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard User</title>
<style>
/* Global Styles */
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Header */
header {
    background: linear-gradient(135deg, #007bff, #0056d6);
    color: white;
    padding: 25px 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
header h2 { margin: 0; font-size: 24px; }
header small { display: block; font-weight: 300; margin-top: 5px; }

/* Navigation */
nav {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
    flex-wrap: wrap;
}
nav a {
    background: white;
    color: #007bff;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 500;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    transition: 0.3s;
}
nav a:hover {
    background: #e6f0ff;
    transform: translateY(-2px);
}

/* Card */
.card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin: 20px auto;
    max-width: 1000px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
}
th, td {
    padding: 12px;
    text-align: center;
}
th {
    background: #007bff;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
tr:nth-child(even) { background: #f9faff; }
tr:hover { background: #eef4ff; transition: 0.2s; }

/* Status */
.hadir {
    background: #d4edda;
    color: #155724;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}
.telat {
    background: #fff3cd;
    color: #856404;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

/* Map iframe */
.map-frame {
    width: 220px;
    height: 140px;
    border: 1px solid #ddd;
    border-radius: 10px;
    transition: transform 0.3s;
}
.map-frame:hover { transform: scale(1.05); }

/* Responsive */
@media screen and (max-width: 768px) {
    .map-frame { width: 100%; height: 200px; }
    nav { flex-direction: column; gap: 15px; }
}

/* Button Container */
ul.buttons {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
}

/* Base Button */
ul.buttons .btn {
    text-decoration: none;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: inline-block;
}

/* Absen Sekarang Button */
.btn-absen {
    background: linear-gradient(135deg, #43e97b, #38f9d7);
}
.btn-absen:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

/* Logout Button */
.btn-logout {
    background: linear-gradient(135deg, #f85032, #e73827);
}
.btn-logout:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

/* Responsive */
@media screen and (max-width: 768px) {
    ul.buttons {
        flex-direction: column;
        gap: 15px;
    }
}

</style>
</head>
<body>

<header>
    <h2>Halo, <?= htmlspecialchars($_SESSION['user']['nama']) ?> 👋</h2>
    <small>Dashboard Absensi Anda</small>
    <ul class="buttons" >
        <li><a href="absen.php" class="btn btn-absen">📍 Absen Sekarang</a></li>
        <li><a href="../logout.php" class="btn btn-logout">Keluar</a></li>
    </ul>
</header>
<div class="card">
    <h3>🕒 Riwayat Absen Terbaru</h3>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Status</th>
            <th>Lokasi</th>
        </tr>
        <?php
        if (mysqli_num_rows($absen) > 0):
            while ($a = mysqli_fetch_assoc($absen)):
                $status = ($a['jam'] <= $jam_masuk) ? 
                    "<span class='hadir'>Hadir</span>" : 
                    "<span class='telat'>Telat</span>";
                $lat = $a['latitude'];
                $lon = $a['longitude'];
        ?>
        <tr>
            <td><?= htmlspecialchars($a['tanggal']) ?></td>
            <td><?= htmlspecialchars($a['jam']) ?></td>
            <td><?= $status ?></td>
            <td>
                <?php if ($lat && $lon): ?>
                    <iframe
                        class="map-frame"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps?q=<?= $lat ?>,<?= $lon ?>&hl=id&z=16&output=embed">
                    </iframe>
                <?php else: ?>
                    <span style="color:#888;">Lokasi tidak tersedia</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4">Belum ada data absensi.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>

<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");

// Set timezone WITA
date_default_timezone_set('Asia/Makassar');

// Ambil pengaturan jam masuk
$set = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
$jam_masuk = $set['jam_masuk'];

// Tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

// Ambil semua user
$users = mysqli_query($koneksi, "SELECT * FROM users WHERE role='user'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - UPT Puskesmas Amuntai Selatan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffffff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #007bff, #0056d6);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-container img {
            height: 100px;
            width: 100px;
            border-radius: 8px;
            background: white;
            padding: 5px;
        }

        header h2 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            display: inline-block;
            color: white;
            background: rgba(255,255,255,0.1);
            margin: 0 6px;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }

        nav a:hover {
            background: white;
            color: #0056d6;
        }

        main {
            padding: 30px 50px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        h3 {
            margin-top: 0;
            color: #0056d6;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
            font-size: 14px;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #007bff;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th, td {
            border: none;
            padding: 10px 12px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9fbff;
        }

        tr:hover {
            background: #eef4ff;
            transition: 0.2s;
        }

        .hadir {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
        }

        .telat {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
        }

        .absen {
            background: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
        }

        iframe {
            width: 180px;
            height: 120px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        small {
            display: block;
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #777;
        }

    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <!-- Pastikan file logo disimpan di folder yang sama dengan file PHP ini -->
            <img src="../img/logo.jpg" alt="Logo Puskesmas">
            <div>
                <h2>UPT Puskesmas Amuntai Selatan</h2>
                <small>Kabupaten Hulu Sungai Utara</small>
            </div>
        </div>
    </header>

    <nav style="text-align:center; background:#0056d6; padding:10px;">
        <a href="users.php">👤 Kelola User</a>
        <a href="set_jam.php">🕓 Atur Jam & Hari Kerja</a>
        <a href="pengaturan.php">📅 Atur Hari Libur</a>
        <a href="rekap.php">📈 Rekap Absensi</a>
        <a href="hapus_absen.php">🗑️ Hapus Data Absen</a>
        <a href="../logout.php">🚪 Logout</a>
    </nav>

    <main>
        <div class="card">
            <h3>Absensi Hari Ini (<?= date('d-m-Y') ?>)</h3>

            <table>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Jam Absen</th>
                    <th>Lokasi</th>
                </tr>

                <?php
                $no = 1;
                while($u = mysqli_fetch_assoc($users)) {
                    $q = mysqli_query($koneksi, "SELECT * FROM absen WHERE user_id='$u[id]' AND tanggal='$tanggal_hari_ini' ORDER BY jam ASC LIMIT 1");
                    $data = mysqli_fetch_assoc($q);

                    if ($data) {
                        $status = ($data['jam'] <= $jam_masuk) ? "<span class='hadir'>Hadir</span>" : "<span class='telat'>Telat</span>";
                        $jam = $data['jam'];

                        if (!empty($data['latitude']) && !empty($data['longitude'])) {
                            $lat = $data['latitude'];
                            $lon = $data['longitude'];
                            $lokasi = "
                                <iframe src='https://www.google.com/maps?q=$lat,$lon&hl=id&z=17&output=embed'></iframe>
                                <small><a href='https://maps.google.com/?q=$lat,$lon' target='_blank'>📍 Lihat di Google Maps</a></small>
                            ";
                        } else {
                            $lokasi = "-";
                        }
                    } else {
                        $status = "<span class='absen'>Tidak Masuk</span>";
                        $jam = "-";
                        $lokasi = "-";
                    }

                    echo "
                    <tr>
                        <td>$no</td>
                        <td>".htmlspecialchars($u['nama'])."</td>
                        <td>$status</td>
                        <td>$jam</td>
                        <td>$lokasi</td>
                    </tr>";
                    $no++;
                }
                ?>
            </table>
        </div>
    </main>

    <footer>
        © <?= date('Y') ?> Sistem Absensi | UPT Puskesmas Amuntai Selatan
    </footer>
</body>
</html>

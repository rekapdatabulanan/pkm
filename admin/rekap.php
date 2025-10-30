<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");

// Ambil pengaturan jam masuk & hari kerja
$set = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
$jam_masuk = $set['jam_masuk'];
$hari_kerja = explode(",", str_replace(" ", "", $set['hari_kerja']));

// Ambil daftar hari libur
$libur_arr = [];
$libur_q = mysqli_query($koneksi, "SELECT tanggal FROM hari_libur");
while($row = mysqli_fetch_assoc($libur_q)) {
    $libur_arr[] = $row['tanggal'];
}

// Tentukan bulan dan tahun
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Hitung jumlah hari dalam bulan tersebut
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil semua user role 'user'
$users = mysqli_query($koneksi, "SELECT * FROM users WHERE role='user'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Rekap Absensi Bulan <?= date('F Y', strtotime("$tahun-$bulan-01")) ?></title>
<style>
/* Global */
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 30px;
    color: #333;
}

h2 {
    text-align: center;
    color: #007bff;
    margin-bottom: 25px;
}

/* Form filter */
form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

form input[type="number"] {
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

form button {
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Links */
a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}


</style>

<style>
body { font-family: Arial, sans-serif; }
table { border-collapse: collapse; width: 100%; font-size: 14px; margin-top: 10px; }
th, td { border: 1px solid #999; padding: 6px; text-align: center; }
th { background: #f2f2f2; }
.hadir { background: #d4edda; }
.telat { background: #fff3cd; }
.absen { background: #f8d7da; }
.libur { background: #d1ecf1; }
.summary { font-weight: bold; background: #eef; }
</style>
</head>
<body>

<h2>📊 Rekap Absensi Bulan <?= date('F Y', strtotime("$tahun-$bulan-01")) ?></h2>

<form method="get">
    Bulan: <input type="number" name="bulan" min="1" max="12" value="<?= $bulan ?>">
    Tahun: <input type="number" name="tahun" value="<?= $tahun ?>">
    <button type="submit">Tampilkan</button>
</form>

<br>
<a href="dashboard.php">⬅ Kembali</a>
<br><br>

<form method="GET" action="cetak_rekap.php" target="_blank">
    <input type="hidden" name="bulan" value="<?= $_GET['bulan'] ?? date('m') ?>">
    <input type="hidden" name="tahun" value="<?= $_GET['tahun'] ?? date('Y') ?>">
    <button type="submit">🖨️ Cetak Rekap Absensi</button>
</form>

<table>
    <tr>
        <th rowspan="2">Nama</th>
        <th colspan="<?= $jumlah_hari ?>">Tanggal</th>
        <th colspan="4">Rekap</th>
    </tr>
    <tr>
        <?php for ($tgl=1; $tgl<=$jumlah_hari; $tgl++): ?>
            <th><?= $tgl ?></th>
        <?php endfor; ?>
        <th>Tidak Masuk</th>
        <th>Telat</th>
        <th>Hadir</th>
        <th>Hari Kerja</th>
    </tr>

<?php while($u = mysqli_fetch_assoc($users)): ?>
    <tr>
        <td><?= htmlspecialchars($u['nama']) ?></td>
        <?php
        $hadir = $telat = $absen = 0;
        $hari_kerja_bulan = 0;

        for ($tgl=1; $tgl <= $jumlah_hari; $tgl++) {
            $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $tgl);
            $hari = date('l', strtotime($tanggal));

            $q = mysqli_query($koneksi, "SELECT * FROM absen WHERE user_id='$u[id]' AND tanggal='$tanggal' ORDER BY jam ASC LIMIT 1");
            $data = mysqli_fetch_assoc($q);

            if ($data) {
                if ($data['jam'] <= $jam_masuk) {
                    $status = "<td class='hadir'>H</td>";
                    $hadir++;
                } else {
                    $status = "<td class='telat'>T</td>";
                    $telat++;
                }
            } else {
                if (in_array($hari, $hari_kerja) && !in_array($tanggal, $libur_arr)) {
                    $status = "<td class='absen'>A</td>";
                    $absen++;
                } else {
                    $status = "<td class='libur'>-</td>"; // libur
                }
            }

            // Hitung hari kerja bulan ini
            if (in_array($hari, $hari_kerja) && !in_array($tanggal, $libur_arr)) {
                $hari_kerja_bulan++;
            }

            echo $status;
        }
        ?>
        <td class="summary"><?= $absen ?></td>
        <td class="summary"><?= $telat ?></td>
        <td class="summary"><?= $hadir ?></td>
        <td class="summary"><?= $hari_kerja_bulan ?></td>
    </tr>
<?php endwhile; ?>

</table>

</body>
</html>

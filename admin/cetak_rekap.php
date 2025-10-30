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
<title></title>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 4px; text-align: center; }
th { background: #f2f2f2; }
.hadir { background: #05fe3fff; }
.telat { background: #fbc104ff; }
.absen { background: #fd0015ff; }
.libur { background: #00d9ffff; }
.summary { font-weight: bold; background: #eef; }
</style>
</head>
<body onload="window.print()">
<h2 align="center">REKAP ABSENSI APEL <br>UPT PUSKESMAS AMUNTAI SELATAN <br> Bulan <?= date('F Y', strtotime("$tahun-$bulan-01")) ?></h2>

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

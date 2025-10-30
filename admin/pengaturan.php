<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");

// Tambah hari libur
if (isset($_POST['tambah_libur'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($koneksi, "INSERT INTO hari_libur (tanggal, keterangan) VALUES ('$tanggal', '$keterangan')");
}

// Hapus hari libur
if (isset($_GET['hapus_libur'])) {
    $id = $_GET['hapus_libur'];
    mysqli_query($koneksi, "DELETE FROM hari_libur WHERE id='$id'");
}

// Ambil semua hari libur
$liburs = mysqli_query($koneksi, "SELECT * FROM hari_libur ORDER BY tanggal ASC");
?>

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

/* Form */
form {
    background: white;
    max-width: 600px;
    margin: 0 auto 30px;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 15px;
}

form input {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
}

form button {
    padding: 12px 0;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #28a745, #218838);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

th {
    background: #007bff;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px;
    text-align: center;
}

td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

tr:hover {
    background: #eef4ff;
    transition: 0.2s;
}

/* Links */
a {
    color: #dc3545;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

/* Responsive */
@media screen and (max-width: 600px) {
    form, table {
        width: 100%;
    }
}
</style>


<h2>Pengaturan Hari Libur</h2>

<form method="post">
    Tanggal: <input type="date" name="tanggal" required>
    Keterangan: <input type="text" name="keterangan" placeholder="Misal: Libur Nasional" required>
    <button name="tambah_libur">Tambah Hari Libur</button>
</form>

<table border="1" cellpadding="5">
    <tr><th>Tanggal</th><th>Keterangan</th><th>Aksi</th></tr>
    <?php while($l = mysqli_fetch_assoc($liburs)): ?>
        <tr>
            
            <td><?= $l['tanggal'] ?></td>
            <td><?= htmlspecialchars($l['keterangan']) ?></td>
            <td>
                <a href="?hapus_libur=<?= $l['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

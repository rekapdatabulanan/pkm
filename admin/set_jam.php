<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");

if (isset($_POST['simpan'])) {
    $masuk = $_POST['jam_masuk'];
    $pulang = $_POST['jam_pulang'];
    $hari = $_POST['hari_kerja'];
    mysqli_query($koneksi, "UPDATE pengaturan SET jam_masuk='$masuk', jam_pulang='$pulang', hari_kerja='$hari' WHERE id=1");
}
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
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
    max-width: 500px;
    margin: 0 auto;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 15px;
}

form label {
    font-weight: 500;
    margin-bottom: 5px;
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
    background: linear-gradient(135deg, #007bff, #0056d6);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

/* Responsive */
@media screen and (max-width: 600px) {
    form {
        padding: 20px;
    }
}
</style>

<h2>Atur Jam & Hari Kerja</h2>
<form method="post">
    Jam Masuk: <input type="time" name="jam_masuk" value="<?= $data['jam_masuk'] ?>"><br>
    Jam Pulang: <input type="time" name="jam_pulang" value="<?= $data['jam_pulang'] ?>"><br>
    Hari Kerja: <input name="hari_kerja" value="<?= $data['hari_kerja'] ?>"><br>
    <button name="simpan">Simpan</button>
</form>

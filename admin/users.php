<?php
include '../config.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: ../login.php");

// Tambah user
if (isset($_POST['tambah']) && !isset($_POST['id'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    mysqli_query($koneksi, "INSERT INTO users VALUES('', '$nama', '$username', '$password', '$role')");
}

// Edit user
if (isset($_POST['tambah']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    mysqli_query($koneksi, "UPDATE users SET nama='$nama', username='$username', password='$password', role='$role' WHERE id='$id'");
}

// Hapus user
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$_GET[hapus]'");
}

// Ambil data user untuk ditampilkan di form jika edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$_GET[edit]'");
    $edit_user = mysqli_fetch_assoc($res);
}

// Ambil semua user
$users = mysqli_query($koneksi, "SELECT * FROM users");
?>

<style>
/* Global */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 30px;
    background: #f4f6f9;
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
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 0 auto 30px auto;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

form input, form select {
    flex: 1 1 calc(50% - 20px);
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
}

form button {
    padding: 12px 25px;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #007bff, #0056d6);
    color: white;
    cursor: pointer;
    transition: 0.3s;
    flex: 1 1 100%;
}
form button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

form a {
    text-decoration: none;
    color: #dc3545;
    font-weight: 600;
    margin-left: 10px;
    transition: 0.3s;
}
form a:hover {
    text-decoration: underline;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    font-size: 14px;
}

th, td {
    padding: 12px 15px;
    text-align: center;
}

th {
    background: #007bff;
    color: white;
    text-transform: uppercase;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

tr:hover {
    background: #e6f0ff;
    transition: 0.2s;
}

/* Action links */
td a {
    text-decoration: none;
    color: #007bff;
    font-weight: 500;
    margin: 0 5px;
    transition: 0.3s;
}
td a:hover {
    color: #0056d6;
    text-decoration: underline;
}

/* Responsive */
@media screen and (max-width: 768px) {
    form input, form select {
        flex: 1 1 100%;
    }
}
</style>

<h2>Kelola User</h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $edit_user['id'] ?? '' ?>">
    Nama: <input name="nama" value="<?= $edit_user['nama'] ?? '' ?>" required>
    Username: <input name="username" value="<?= $edit_user['username'] ?? '' ?>" required>
    Password: <input name="password" value="<?= $edit_user['password'] ?? '' ?>" required>
    Role:
    <select name="role" required>
        <option value="user" <?= (isset($edit_user['role']) && $edit_user['role']=='user') ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= (isset($edit_user['role']) && $edit_user['role']=='admin') ? 'selected' : '' ?>>Admin</option>
    </select>
    <button name="tambah"><?= isset($edit_user) ? 'Update' : 'Tambah' ?></button>
    <?php if(isset($edit_user)): ?>
        <a href="kelola_user.php">Batal</a>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
<?php while($u = mysqli_fetch_assoc($users)): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= $u['nama'] ?></td>
    <td><?= $u['username'] ?></td>
    <td><?= $u['role'] ?></td>
    <td>
        <a href="?edit=<?= $u['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $u['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

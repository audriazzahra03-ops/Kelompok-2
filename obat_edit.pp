<?php
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

// ambil data
$stmt = $pdo->prepare("SELECT * FROM obat WHERE id = ?");
$stmt->execute([$id]);
$o = $stmt->fetch();

if (!$o) {
    die("Data tidak ditemukan");
}

// update
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $expired = $_POST['expired'];

    $stmt = $pdo->prepare("UPDATE obat SET nama=?, jenis=?, harga=?, stok=?, expired=? WHERE id=?");
    $stmt->execute([$nama, $jenis, $harga, $stok, $expired, $id]);

    header("Location: index.php?page=obat");
    exit;
}
?>

<h2>Edit Obat</h2>

<form method="post">
    <input type="text" name="nama" value="<?= $o['nama'] ?>" required><br><br>
    <input type="text" name="jenis" value="<?= $o['jenis'] ?>" required><br><br>
    <input type="number" name="harga" value="<?= $o['harga'] ?>" required><br><br>
    <input type="number" name="stok" value="<?= $o['stok'] ?>" required><br><br>
    Expired:
    <input type="date" name="expired" value="<?= $o['expired'] ?>"><br><br>

    <button type="submit" name="update">Simpan Perubahan</button>
</form>

<?php
// ===== Ambil data obat =====
$stmt = $pdo->query("SELECT * FROM obat ORDER BY id DESC");
$obat = $stmt->fetchAll();
?>

<h2>Data Obat</h2>

<!-- Form Tambah Obat -->
<form action="" method="post" style="margin-bottom:20px;">
    <input type="text" name="nama" placeholder="Nama Obat" required>
    <input type="text" name="jenis" placeholder="Jenis Obat" required>
    <input type="number" name="harga" placeholder="Harga" required>
    <input type="number" name="stok" placeholder="Stok" required>
    Expired: <input type="date" name="expired">
    <button type="submit" name="tambah">Tambah Obat</button>
</form>

<?php
// ===== Tambah Data =====
if (isset($_POST['tambah'])) {
    $stmt = $pdo->prepare("INSERT INTO obat (nama, jenis, harga, stok, expired) VALUES (?,?,?,?,?)");
    $stmt->execute([
        $_POST['nama'],
        $_POST['jenis'],
        $_POST['harga'],
        $_POST['stok'],
        $_POST['expired']
    ]);

    echo "<meta http-equiv='refresh' content='0'>";
}

// ===== Hapus Data =====
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM obat WHERE id=?")->execute([$_GET['delete']]);
    echo "<meta http-equiv='refresh' content='0'>";
}

// ===== Ambil Data untuk Edit =====
$editMode = false;
if (isset($_GET['edit'])) {
    $editMode = true;
    $id_edit = $_GET['edit'];

    $q = $pdo->prepare("SELECT * FROM obat WHERE id=?");
    $q->execute([$id_edit]);
    $editData = $q->fetch();
}

// ===== Proses Edit =====
if (isset($_POST['update'])) {
    $sql = $pdo->prepare("UPDATE obat SET nama=?, jenis=?, harga=?, stok=?, expired=? WHERE id=?");
    $sql->execute([
        $_POST["nama"],
        $_POST["jenis"],
        $_POST["harga"],
        $_POST["stok"],
        $_POST["expired"],
        $_POST["id"]
    ]);

    echo "<meta http-equiv='refresh' content='0'>";
}
?>

<!-- FORM EDIT -->
<?php if ($editMode): ?>
    <br><br>
    <h3>Edit Obat</h3>

    <form method="post">
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">

        <input type="text" name="nama" value="<?= $editData['nama'] ?>" required>
        <input type="text" name="jenis" value="<?= $editData['jenis'] ?>" required>
        <input type="number" name="harga" value="<?= $editData['harga'] ?>" required>
        <input type="number" name="stok" value="<?= $editData['stok'] ?>" required>
        Expired: <input type="date" name="expired" value="<?= $editData['expired'] ?>">

        <button type="submit" name="update">Simpan</button>
    </form>
<?php endif; ?>

<br><br>

<table border="1" width="100%" cellpadding="5" cellspacing="0">
    <tr style="background:#3b82f6;color:white;">
        <th>Nama</th>
        <th>Jenis</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Expired</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

<?php
$today = date("Y-m-d");

foreach ($obat as $o):

    // =======================
    // STATUS EXPIRY & STOK
    // =======================

    $row_color = "";
    $status = "Aman";

    // --- Expired value ---
    if (!empty($o["expired"])) {
        $exp = date("Y-m-d", strtotime($o["expired"]));
        $exp_tampil = date("d-m-Y", strtotime($o["expired"]));
    } else {
        $exp = null;
        $exp_tampil = "-";
    }

    // --- Cek Expired ---
    if ($exp) {
        $diff_days = (strtotime($exp) - strtotime($today)) / 86400;

        if ($diff_days <= 0) {
            $status = "Expired";
            $row_color = "style='background:#fecaca'"; // merah

        } elseif ($diff_days <= 30) {
            $status = "Hampir Expired";
            $row_color = "style='background:#fef3c7'"; // kuning
        }
    }

    // --- Cek Stok Menipis ---
    if ($o["stok"] <= 10) {

        if ($status == "Expired") {
            $status = "Expired & Stok Menipis";
            $row_color = "style='background:#fecaca'";

        } elseif ($status == "Hampir Expired") {
            $status = "Hampir Expired & Stok Menipis";
            $row_color = "style='background:#fef3c7'";

        } else {
            $status = "Stok Menipis";
            $row_color = "style='background:#fde68a'";
        }
    }
?>
    <tr <?= $row_color ?>>
        <td><?= $o["nama"] ?></td>
        <td><?= $o["jenis"] ?></td>
        <td>Rp <?= number_format($o["harga"],0,',','.') ?></td>
        <td><?= $o["stok"] ?></td>
        <td><?= $exp_tampil ?></td>
        <td><?= $status ?></td>

        <td>
            <a href="index.php?page=obat&edit=<?= $o['id'] ?>">Edit</a> | 
            <a href="index.php?page=obat&delete=<?= $o['id'] ?>" onclick="return confirm('Yakin hapus obat?')">Hapus</a>
        </td>
    </tr>

<?php endforeach; ?>

</table>

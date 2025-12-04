 ER<?php
// handle create
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='add'){
  $nama = $_POST['nama'] ?? '';
  $alamat = $_POST['alamat'] ?? '';
  $telp = $_POST['telp'] ?? '';
  $obatIds = $_POST['obatIds'] ?? [];
  $stmt = $pdo->prepare('INSERT INTO supplier (nama,alamat,telp) VALUES (?,?,?)');
  $stmt->execute([$nama,$alamat,$telp]);
  $sid = $pdo->lastInsertId();
  if(!empty($obatIds)){
    $ins = $pdo->prepare('INSERT INTO supplier_obat (supplier_id, obat_id) VALUES (?,?)');
    foreach($obatIds as $oid){
      $ins->execute([$sid,(int)$oid]);
    }
  }
  header('Location: index.php?page=supplier');
  exit;
}
// delete
if(isset($_GET['delete'])){
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM supplier WHERE id=?');
  $stmt->execute([$id]);
  header('Location: index.php?page=supplier');
  exit;
}
// fetch data for listing
$obats = $pdo->query('SELECT id,nama FROM obat ORDER BY nama')->fetchAll();
$suppliers = $pdo->query('SELECT * FROM supplier ORDER BY id DESC')->fetchAll();
?>
<section>
  <h2>Data Supplier</h2>
  <div class="form-card">
    <form method="post" action="index.php?page=supplier">
      <input type="hidden" name="action" value="add">
      <input type="text" name="nama" placeholder="Nama Supplier" required>
      <input type="text" name="alamat" placeholder="Alamat">
      <input type="text" name="telp" placeholder="No. Telepon">
      <label>Pilih Obat (CTRL/CMD + klik untuk pilih banyak):</label>
      <select name="obatIds[]" multiple style="height:100px;padding:8px;border-radius:6px;">
        <?php foreach($obats as $o): ?>
          <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nama']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Tambah Supplier</button>
    </form>
  </div>

  <table>
    <tr><th>Nama</th><th>Alamat</th><th>Telp</th><th>Obat</th><th>Aksi</th></tr>
    <?php foreach($suppliers as $s): ?>
      <?php
        $stmt = $pdo->prepare('SELECT o.nama FROM obat o JOIN supplier_obat so ON so.obat_id=o.id WHERE so.supplier_id=?');
        $stmt->execute([$s['id']]);
        $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
      ?>
      <tr>
        <td><?= htmlspecialchars($s['nama']) ?></td>
        <td><?= htmlspecialchars($s['alamat']) ?: '-' ?></td>
        <td><?= htmlspecialchars($s['telp']) ?: '-' ?></td>
        <td><?= $names ? htmlspecialchars(implode(', ',$names)) : '-' ?></td>
        <td>
          <a class="btn small" href="index.php?page=supplier&edit=<?= $s['id'] ?>">Edit</a>
          <a class="btn small" href="index.php?page=supplier&delete=<?= $s['id'] ?>" onclick="return confirm('Hapus supplier?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if(isset($_GET['edit'])):
    $eid = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM supplier WHERE id=?'); $stmt->execute([$eid]); $row = $stmt->fetch();
    if($row):
      // fetch obat ids
      $stmt2 = $pdo->prepare('SELECT obat_id FROM supplier_obat WHERE supplier_id=?');
      $stmt2->execute([$eid]); $have = $stmt2->fetchAll(PDO::FETCH_COLUMN);
  ?>
    <h3>Edit Supplier</h3>
    <div class="form-card">
      <form method="post" action="index.php?page=supplier">
        <input type="hidden" name="action" value="edit_save">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="text" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
        <input type="text" name="alamat" value="<?= htmlspecialchars($row['alamat']) ?>" >
        <input type="text" name="telp" value="<?= htmlspecialchars($row['telp']) ?>" >
        <label>Pilih Obat (CTRL/CMD + klik untuk pilih banyak):</label>
        <select name="obatIds[]" multiple style="height:100px;padding:8px;border-radius:6px;">
          <?php foreach($obats as $o): ?>
            <option value="<?= $o['id'] ?>" <?= in_array($o['id'],$have) ? 'selected' : '' ?>><?= htmlspecialchars($o['nama']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Simpan Perubahan</button>
      </form>
    </div>
  <?php endif; endif; ?>

<?php
// handle edit save (separate, after form)
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='edit_save'){
  $id = (int)($_POST['id'] ?? 0);
  $nama = $_POST['nama'] ?? '';
  $alamat = $_POST['alamat'] ?? '';
  $telp = $_POST['telp'] ?? '';
  $obatIds = $_POST['obatIds'] ?? [];
  $stmt = $pdo->prepare('UPDATE supplier SET nama=?,alamat=?,telp=? WHERE id=?');
  $stmt->execute([$nama,$alamat,$telp,$id]);
  // delete old mapping
  $pdo->prepare('DELETE FROM supplier_obat WHERE supplier_id=?')->execute([$id]);
  if(!empty($obatIds)){
    $ins = $pdo->prepare('INSERT INTO supplier_obat (supplier_id, obat_id) VALUES (?,?)');
    foreach($obatIds as $oid){
      $ins->execute([$id,(int)$oid]);
    }
  }
  header('Location: index.php?page=supplier');
  exit;
}

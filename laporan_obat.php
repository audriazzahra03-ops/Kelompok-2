<?php
// Ambil filter jenis jika ada
$filterJenis = $_GET['jenis'] ?? '';

// Query data obat
if ($filterJenis) {
    $stmt = $pdo->prepare("SELECT * FROM obat WHERE jenis = ? ORDER BY nama ASC");
    $stmt->execute([$filterJenis]);
    $obats = $stmt->fetchAll();
} else {
    $obats = $pdo->query("SELECT * FROM obat ORDER BY nama ASC")->fetchAll();
}

// Ambil semua jenis untuk dropdown filter
$jenisList = $pdo->query("SELECT DISTINCT jenis FROM obat ORDER BY jenis ASC")->fetchAll();
?>
<section>
    <h2>Laporan Data Obat</h2>

    <!-- FILTER -->
    <div class="form-card">
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="laporan_obat">
            <label>Pilih Jenis Obat:</label>
            <select name="jenis">
                <option value="">Semua Jenis</option>
                <?php foreach($jenisList as $j): ?>
                    <option value="<?= htmlspecialchars($j['jenis']) ?>" 
                        <?= $filterJenis == $j['jenis'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($j['jenis']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
            <a href="index.php?page=laporan_obat" class="btn">Reset</a>
        </form>
    </div>

    <!-- TABLE -->
    <table>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Jenis</th>
            <th>Harga</th>
            <th>Stok</th>
        </tr>

        <?php if(count($obats) == 0): ?>
            <tr><td colspan="5" style="text-align:center;">Tidak ada data</td></tr>
        <?php endif; ?>

        <?php $no=1; foreach($obats as $o): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($o['nama']) ?></td>
            <td><?= htmlspecialchars($o['jenis']) ?></td>
            <td><?= $o['harga'] ? 'Rp '.number_format($o['harga'], 2, ',', '.') : '-' ?></td>
            <td><?= (int)$o['stok'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- CETAK PDF (Versi Print Sederhana) -->
    <br>
    <a href="pages/laporan_obat_pdf.php" target="_blank" class="btn">Cetak PDF</a>

</section>

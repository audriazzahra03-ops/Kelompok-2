<?php
require_once __DIR__ . '/../inc/db.php';

// Ambil data
$obats = $pdo->query("SELECT * FROM obat ORDER BY nama ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Obat</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body onload="window.print()">

<h2>LAPORAN DATA OBAT</h2>

<table>
    <tr>
        <th>No</th>
        <th>Nama Obat</th>
        <th>Jenis</th>
        <th>Harga</th>
        <th>Stok</th>
    </tr>

    <?php 
    $no = 1;
    foreach($obats as $o): 
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($o['nama']) ?></td>
        <td><?= htmlspecialchars($o['jenis']) ?></td>
        <td><?= $o['harga'] ? 'Rp '.number_format($o['harga'],2,',','.') : '-' ?></td>
        <td><?= (int)$o['stok'] ?></td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>

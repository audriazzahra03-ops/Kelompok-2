<?php
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM obat WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?page=obat");
exit;
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../config/database.php';

$id     = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

// validasi status biar gak sembarangan diisi lewat URL
$statusValid = ['menunggu', 'dipanggil', 'selesai'];

if ($id && in_array($status, $statusValid)) {
    $stmt = $pdo->prepare("UPDATE pasien SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header('Location: ../pendaftaran.php');
exit;
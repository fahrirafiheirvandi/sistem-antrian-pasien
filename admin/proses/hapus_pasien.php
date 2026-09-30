<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // ambil nama dulu sebelum dihapus, buat ditampilin di pesan sukses
    $stmt = $pdo->prepare("SELECT nama FROM pasien WHERE id = ?");
    $stmt->execute([$id]);
    $nama = $stmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM pasien WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['sukses'] = $nama ? "Data pasien \"$nama\" berhasil dihapus." : "Data pasien berhasil dihapus.";
}

header('Location: ../pendaftaran.php');
exit;
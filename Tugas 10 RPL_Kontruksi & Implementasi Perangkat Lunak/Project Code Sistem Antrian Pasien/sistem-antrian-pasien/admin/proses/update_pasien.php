<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pendaftaran.php');
    exit;
}

$id       = $_POST['id'];
$nama     = trim($_POST['nama']);
$usia     = (int) $_POST['usia'];
$no_hp    = trim($_POST['no_hp']);
$keluhan  = trim($_POST['keluhan']);
$kategori = $_POST['kategori'];

$stmt = $pdo->prepare("
    UPDATE pasien
    SET nama = ?, usia = ?, no_hp = ?, keluhan = ?, kategori = ?
    WHERE id = ?
");
$stmt->execute([$nama, $usia, $no_hp, $keluhan, $kategori, $id]);

$_SESSION['sukses'] = "Data pasien \"$nama\" berhasil diperbarui.";
header('Location: ../pendaftaran.php');
exit;
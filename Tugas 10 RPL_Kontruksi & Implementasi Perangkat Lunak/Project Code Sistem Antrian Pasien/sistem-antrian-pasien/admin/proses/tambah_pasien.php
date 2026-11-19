<?php
session_start();
require_once '../../config/database.php';

// halaman ini cuma boleh diakses dari submit form (POST), gak boleh diakses langsung
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pendaftaran.php');
    exit;
}

$nama     = trim($_POST['nama']);
$usia     = (int) $_POST['usia'];
$no_hp    = trim($_POST['no_hp']);
$keluhan  = trim($_POST['keluhan']);
$kategori = $_POST['kategori'];

// generate no tiket otomatis, formatnya: TKT-20260926-001
// urutannya di-reset tiap hari (liat dari jumlah tiket yang udah ada di tanggal itu)
$tanggal = date('Ymd');
$prefix  = "TKT-$tanggal-";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pasien WHERE no_tiket LIKE ?");
$stmt->execute(["$prefix%"]);
$urutan   = $stmt->fetchColumn() + 1;
$no_tiket = $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);

// simpan ke database (pake prepared statement biar aman dari SQL Injection)
$stmt = $pdo->prepare("
    INSERT INTO pasien (no_tiket, nama, usia, no_hp, keluhan, kategori)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$no_tiket, $nama, $usia, $no_hp, $keluhan, $kategori]);

// simpan pesan sukses buat ditampilin di halaman pendaftaran
$_SESSION['sukses'] = "Pasien berhasil didaftarkan dengan nomor tiket $no_tiket";

// simpan data pasien yang baru didaftarkan, dipakai buat auto-munculin QR code-nya
$_SESSION['new_tiket'] = $no_tiket;
$_SESSION['new_nama']  = $nama;

header('Location: ../pendaftaran.php');
exit;
<?php
session_start();

// Proteksi: kalau belum login, tendang ke halaman login
// Ini ditaro di header.php aja biar semua halaman admin otomatis ke-protect
// tanpa perlu nulis ulang pengecekan ini di tiap file
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

// Ambil role terbaru dari database (bukan dari session), jadi perubahan role
// atau akun yang dihapus langsung berlaku tanpa harus login ulang
$stmtRole = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmtRole->execute([$_SESSION['user_id']]);
$roleSaya = $stmtRole->fetchColumn();

if ($roleSaya === false) {
    // akun sudah dihapus oleh superadmin -> paksa keluar
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$_SESSION['role'] = $roleSaya;
$isSuperadmin = ($roleSaya === 'superadmin');

// halaman yang menyetel $hanyaSuperadmin = true cuma boleh dibuka superadmin
if (!empty($hanyaSuperadmin) && !$isSuperadmin) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Admin' ?> - Sistem Antrian Pasien</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/vendor/fontawesome/css/all.min.css">
    <script src="../css/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<body>
<div class="wrapper">

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h3><?= $pageTitle ?? '' ?></h3>
            <div class="topbar-user" id="profileToggle">
                <i class="fa-solid fa-circle-user"></i>
                <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <i class="fa-solid fa-chevron-down dropdown-arrow"></i>

                <div class="profile-dropdown" id="profileDropdown">
                    <a href="ganti_password.php">
                        <i class="fa-solid fa-key"></i> Ganti Password
                    </a>
                    <a href="../logout.php" class="logout-link js-confirm"
   data-title="Keluar dari Sistem?"
   data-text="Anda yakin mau keluar dari halaman admin?"
   data-icon="question"
   data-confirm-text="Ya, Keluar"
   data-confirm-color="#c62828">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
</a>
                </div>
            </div>
        </div>

        <div class="content-body">
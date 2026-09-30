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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Admin' ?> - Sistem Antrian Pasien</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="../logout.php" class="logout-link">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
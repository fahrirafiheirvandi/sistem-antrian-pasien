<?php
// Dipanggil di file proses yang hanya boleh dijalankan superadmin.
// Syarat: session_start() dan koneksi $pdo sudah ada sebelum file ini dipanggil.
$stmtCek = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmtCek->execute([$_SESSION['user_id']]);

if ($stmtCek->fetchColumn() !== 'superadmin') {
    header('Location: ../dashboard.php');
    exit;
}
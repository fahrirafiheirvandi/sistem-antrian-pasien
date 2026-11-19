<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../config/database.php';
require_once '../includes/cek_superadmin.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id === (int) $_SESSION['user_id']) {
    $_SESSION['error_user'] = 'Akun yang sedang Anda pakai tidak bisa dihapus.';
} elseif ($id > 0) {
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $target = $stmt->fetch();

    if ($target && $target['role'] === 'superadmin') {
        $_SESSION['error_user'] = 'Akun superadmin tidak bisa dihapus.';
    } elseif ($target) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['sukses_user'] = "Pengguna \"{$target['username']}\" berhasil dihapus.";
    }
}

header('Location: ../pengguna.php');
exit;
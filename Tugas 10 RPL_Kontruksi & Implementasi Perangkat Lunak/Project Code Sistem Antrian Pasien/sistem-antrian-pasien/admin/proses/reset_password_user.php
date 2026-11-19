<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../includes/cek_superadmin.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pengguna.php');
    exit;
}

$id       = (int) ($_POST['id'] ?? 0);
$password = $_POST['password_baru'] ?? '';

if ($id <= 0 || strlen($password) < 6) {
    $_SESSION['error_user'] = 'Password baru minimal 6 karakter.';
} else {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([password_hash($password, PASSWORD_BCRYPT), $id]);
    $_SESSION['sukses_user'] = 'Password pengguna berhasil direset.';
}

header('Location: ../pengguna.php');
exit;

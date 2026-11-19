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

$nama     = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($nama === '' || !preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {
    $_SESSION['error_user'] = 'Nama wajib diisi, dan username harus 3-50 karakter (huruf, angka, titik, underscore).';
} elseif (strlen($password) < 6) {
    $_SESSION['error_user'] = 'Password minimal 6 karakter.';
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error_user'] = "Username \"$username\" sudah dipakai, coba yang lain.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
        $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $nama]);
        $_SESSION['sukses_user'] = "Pengguna \"$username\" berhasil ditambahkan.";
    }
}

header('Location: ../pengguna.php');
exit;

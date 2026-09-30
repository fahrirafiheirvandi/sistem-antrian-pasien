<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$passwordLama  = $_POST['password_lama'] ?? '';
$passwordBaru  = $_POST['password_baru'] ?? '';
$konfirmasi    = $_POST['konfirmasi_password'] ?? '';

// ambil data user yang lagi login dari database (bukan dari session,
// biar data password-nya selalu yang paling baru)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// 1. Cek password lama bener apa nggak
if (!password_verify($passwordLama, $user['password'])) {
    $_SESSION['error_password'] = 'Password lama yang Anda masukkan salah.';
    header('Location: ../ganti_password.php');
    exit;
}

// 2. Cek konfirmasi password baru cocok
if ($passwordBaru !== $konfirmasi) {
    $_SESSION['error_password'] = 'Konfirmasi password baru tidak cocok.';
    header('Location: ../ganti_password.php');
    exit;
}

// 3. Cek panjang password minimal 6 karakter
if (strlen($passwordBaru) < 6) {
    $_SESSION['error_password'] = 'Password baru minimal 6 karakter.';
    header('Location: ../ganti_password.php');
    exit;
}

// Semua validasi lolos, hash password baru terus simpan ke database
$hashBaru = password_hash($passwordBaru, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$hashBaru, $_SESSION['user_id']]);

$_SESSION['sukses_password'] = 'Password berhasil diubah.';
header('Location: ../ganti_password.php');
exit;
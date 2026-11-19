<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
require_once '../../config/database.php';
require_once '../includes/cek_superadmin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pengguna.php');
    exit;
}

$id       = (int) ($_POST['id'] ?? 0);
$nama     = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');

if ($id <= 0 || $nama === '' || !preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {
    $_SESSION['error_user'] = 'Nama wajib diisi, dan username harus 3-50 karakter (huruf, angka, titik, underscore).';
} else {
    // cek username belum dipakai orang lain (selain akun ini sendiri)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?");
    $stmt->execute([$username, $id]);

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error_user'] = "Username \"$username\" sudah dipakai, coba yang lain.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, username = ? WHERE id = ?");
        $stmt->execute([$nama, $username, $id]);

        // kalau yang diedit akun sendiri, perbarui juga data di session
        if ($id === (int) $_SESSION['user_id']) {
            $_SESSION['nama']     = $nama;
            $_SESSION['username'] = $username;
        }

        $_SESSION['sukses_user'] = 'Data pengguna berhasil diperbarui.';
    }
}

header('Location: ../pengguna.php');
exit;
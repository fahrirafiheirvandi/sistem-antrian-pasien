<?php
session_start();
require_once 'config/database.php';

// Cuma boleh diakses via method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

// Cari user berdasarkan username
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Login berhasil, simpan data ke session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['nama']      = $user['nama_lengkap'];
    $_SESSION['role']      = $user['role'];

    header('Location: admin/dashboard.php');
    exit;
} else {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

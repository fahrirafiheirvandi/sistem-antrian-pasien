<?php
// =========================================================
// Konfigurasi koneksi database
// Sesuaikan host, dbname, username, password kalau beda
// =========================================================

$host = 'localhost';
$dbname = 'db_antrian_pasien';
$db_username = 'root';
$db_password = ''; // default XAMPP/Laragon biasanya kosong

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $db_username,
        $db_password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

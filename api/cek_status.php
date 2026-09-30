<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/fungsi_antrian.php';

header('Content-Type: application/json');

$tiket = trim($_GET['tiket'] ?? '');

if ($tiket === '') {
    echo json_encode(['found' => false]);
    exit;
}

$hasil = ambilStatusPasien($pdo, $tiket);

if (!$hasil['found']) {
    echo json_encode(['found' => false]);
    exit;
}

echo json_encode([
    'found'  => true,
    'nama'   => $hasil['pasien']['nama'],
    'status' => $hasil['pasien']['status'],
    'posisi' => $hasil['posisi'],
]);
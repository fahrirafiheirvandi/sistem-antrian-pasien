<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

// filter-nya sama persis kayak di data_pasien.php, jadi hasil export = yang lagi tampil di layar
$cari     = trim($_GET['cari'] ?? '');
$status   = $_GET['status'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$dari     = $_GET['dari'] ?? '';
$sampai   = $_GET['sampai'] ?? '';

if (!in_array($status, ['menunggu', 'dipanggil', 'selesai'], true)) $status = '';
if (!in_array($kategori, ['darurat', 'prioritas', 'reguler'], true)) $kategori = '';

$where = [];
$params = [];
if ($cari !== '') {
    $where[] = '(nama LIKE ? OR no_tiket LIKE ? OR no_hp LIKE ?)';
    $kw = "%$cari%";
    array_push($params, $kw, $kw, $kw);
}
if ($status !== '')   { $where[] = 'status = ?';   $params[] = $status; }
if ($kategori !== '') { $where[] = 'kategori = ?'; $params[] = $kategori; }
if ($dari !== '')     { $where[] = 'DATE(created_at) >= ?'; $params[] = $dari; }
if ($sampai !== '')   { $where[] = 'DATE(created_at) <= ?'; $params[] = $sampai; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT no_tiket, nama, usia, no_hp, kategori, keluhan, status, created_at FROM pasien $whereSql ORDER BY created_at DESC");
$stmt->execute($params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="data-pasien-' . date('Ymd-His') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM biar Excel baca UTF-8 dengan benar
fputcsv($out, ['No. Tiket', 'Nama', 'Usia', 'No. HP', 'Kategori', 'Keluhan', 'Status', 'Waktu Daftar']);

// cegah CSV injection: sel yang diawali = + - @ dikasih tanda petik di depan
function amanCsv($v) {
    $v = (string) $v;
    return ($v !== '' && strpos("=+-@", $v[0]) !== false) ? "'" . $v : $v;
}

while ($row = $stmt->fetch()) {
    fputcsv($out, array_map('amanCsv', $row));
}
fclose($out);

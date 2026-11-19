<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Nama pasien disamarkan (contoh: "Budi Santoso" -> "B*** S******")
// karena layar ini ditampilin di ruang tunggu dan bisa dilihat banyak orang
function samarkanNama(string $nama): string
{
    $kata = preg_split('/\s+/', trim($nama));
    $hasil = array_map(function ($k) {
        $panjang = mb_strlen($k);
        return mb_substr($k, 0, 1) . str_repeat('*', max(0, min($panjang - 1, 5)));
    }, $kata);
    return implode(' ', $hasil);
}

// yang lagi dipanggil: ambil 3 terakhir, yang paling baru di paling atas
$dipanggil = $pdo->query("
    SELECT no_tiket, nama, kategori FROM pasien
    WHERE status = 'dipanggil'
    ORDER BY updated_at DESC, id DESC
    LIMIT 3
")->fetchAll();

// urutan menunggu sama persis dengan logika di pendaftaran.php & fungsi_antrian.php
$menunggu = $pdo->query("
    SELECT no_tiket, nama, kategori FROM pasien
    WHERE status = 'menunggu'
    ORDER BY FIELD(kategori, 'darurat', 'prioritas', 'reguler'), created_at ASC
    LIMIT 6
")->fetchAll();

$jumlahMenunggu = (int) $pdo->query("SELECT COUNT(*) FROM pasien WHERE status = 'menunggu'")->fetchColumn();

$ubah = fn($row) => [
    'tiket'    => $row['no_tiket'],
    'nama'     => samarkanNama($row['nama']),
    'kategori' => $row['kategori'],
];

echo json_encode([
    'dipanggil'       => array_map($ubah, $dipanggil),
    'menunggu'        => array_map($ubah, $menunggu),
    'jumlah_menunggu' => $jumlahMenunggu,
]);

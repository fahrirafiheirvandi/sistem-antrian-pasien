<?php
/**
 * Ambil data pasien + hitung posisi antriannya berdasarkan nomor tiket.
 * Dipake bareng di cek-antrian.php (tampilan awal) dan api/cek_status.php (auto-refresh),
 * biar logic-nya gak ke-copas 2 kali di 2 tempat.
 *
 * @param PDO $pdo
 * @param string $tiket
 * @return array ['found' => bool, 'pasien' => array|null, 'posisi' => int|null]
 */
function ambilStatusPasien(PDO $pdo, string $tiket): array
{
    $stmt = $pdo->prepare("SELECT * FROM pasien WHERE no_tiket = ?");
    $stmt->execute([$tiket]);
    $pasien = $stmt->fetch();

    if (!$pasien) {
        return ['found' => false, 'pasien' => null, 'posisi' => null];
    }

    $posisi = null;

    if ($pasien['status'] === 'menunggu') {
        // hitung ada berapa pasien di depan dia:
        // - semua pasien dengan kategori LEBIH prioritas (darurat > prioritas > reguler)
        // - atau kategori sama tapi daftar lebih dulu
        $stmt2 = $pdo->prepare("
            SELECT COUNT(*) FROM pasien
            WHERE status = 'menunggu'
            AND (
                FIELD(kategori, 'darurat', 'prioritas', 'reguler') < FIELD(?, 'darurat', 'prioritas', 'reguler')
                OR (kategori = ? AND created_at < ?)
            )
        ");
        $stmt2->execute([$pasien['kategori'], $pasien['kategori'], $pasien['created_at']]);
        $posisi = $stmt2->fetchColumn() + 1; // +1 biar keitung dirinya sendiri
    }

    return ['found' => true, 'pasien' => $pasien, 'posisi' => $posisi];
}
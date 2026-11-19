<?php
$pageTitle = 'Laporan Pasien';
include 'includes/header.php';

// default tampilin laporan hari ini, tapi bisa difilter pake tanggal lain
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT * FROM pasien
    WHERE status = 'selesai' AND DATE(created_at) = ?
    ORDER BY created_at DESC
");
$stmt->execute([$tanggal]);
$riwayat = $stmt->fetchAll();
?>

<div class="filter-section">
    <form method="GET" class="filter-form">
        <label>Filter Tanggal:</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <button type="submit" class="btn-submit"><i class="fa-solid fa-filter"></i> Tampilkan</button>
    </form>
</div>

<div class="table-section">
    <table class="table-antrian">
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Nama</th>
                <th>Usia</th>
                <th>Kategori</th>
                <th>Keluhan</th>
                <th>Waktu Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada riwayat pasien selesai di tanggal ini.</td></tr>
            <?php else: ?>
                <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['no_tiket']) ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= $r['usia'] ?></td>
                    <td><span class="badge badge-<?= $r['kategori'] ?>"><?= ucfirst($r['kategori']) ?></span></td>
                    <td><?= htmlspecialchars($r['keluhan']) ?></td>
                    <td><?= date('H:i', strtotime($r['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
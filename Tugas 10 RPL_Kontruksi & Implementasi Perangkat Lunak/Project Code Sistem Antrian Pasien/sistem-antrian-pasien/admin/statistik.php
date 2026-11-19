<?php
$pageTitle = 'Statistik';
include 'includes/header.php';

// ---------- 1. Jumlah pasien per hari (7 hari terakhir) ----------
$stmt = $pdo->query("
    SELECT DATE(created_at) AS tgl, COUNT(*) AS total
    FROM pasien
    WHERE created_at >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(created_at)
");
$perHari = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// isi hari yang kosong dengan 0 biar grafiknya tetap 7 titik
$labelHari = [];
$dataHari  = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl         = date('Y-m-d', strtotime("-$i day"));
    $labelHari[] = date('d/m', strtotime($tgl));
    $dataHari[]  = (int) ($perHari[$tgl] ?? 0);
}

// ---------- 2. Komposisi kategori (30 hari terakhir) ----------
$stmt = $pdo->query("
    SELECT kategori, COUNT(*) AS total
    FROM pasien
    WHERE created_at >= CURDATE() - INTERVAL 29 DAY
    GROUP BY kategori
");
$perKategori = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$dataKategori = [
    (int) ($perKategori['darurat']   ?? 0),
    (int) ($perKategori['prioritas'] ?? 0),
    (int) ($perKategori['reguler']   ?? 0),
];

// ---------- 3. Jam tersibuk (30 hari terakhir) ----------
$stmt = $pdo->query("
    SELECT HOUR(created_at) AS jam, COUNT(*) AS total
    FROM pasien
    WHERE created_at >= CURDATE() - INTERVAL 29 DAY
    GROUP BY HOUR(created_at)
");
$perJam = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$labelJam = [];
$dataJam  = [];
for ($j = 6; $j <= 21; $j++) { // jam 06.00 - 21.00 cukup buat jam operasional klinik
    $labelJam[] = sprintf('%02d:00', $j);
    $dataJam[]  = (int) ($perJam[$j] ?? 0);
}

// ---------- 4. Angka ringkasan ----------
$total30 = (int) $pdo->query("SELECT COUNT(*) FROM pasien WHERE created_at >= CURDATE() - INTERVAL 29 DAY")->fetchColumn();

// updated_at ikut berubah tiap data diedit/diubah statusnya, jadi ini perkiraan kasar
// waktu dari daftar sampai ditandai selesai, bukan durasi pemeriksaan yang presisi
$rata = $pdo->query("
    SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at))
    FROM pasien
    WHERE status = 'selesai' AND updated_at IS NOT NULL
      AND created_at >= CURDATE() - INTERVAL 29 DAY
")->fetchColumn();
$rataMenit = $rata !== null ? round((float) $rata) : null;

$persenSelesai = 0;
if ($total30 > 0) {
    $selesai30 = (int) $pdo->query("SELECT COUNT(*) FROM pasien WHERE status = 'selesai' AND created_at >= CURDATE() - INTERVAL 29 DAY")->fetchColumn();
    $persenSelesai = round($selesai30 / $total30 * 100);
}
?>

<div class="card-grid" style="margin-bottom: 20px;">
    <div class="summary-card">
        <i class="fa-solid fa-users"></i>
        <div><h2><?= $total30 ?></h2><p>Total Pasien (30 Hari)</p></div>
    </div>
    <div class="summary-card success">
        <i class="fa-solid fa-circle-check"></i>
        <div><h2><?= $persenSelesai ?>%</h2><p>Sudah Selesai Dilayani</p></div>
    </div>
    <div class="summary-card">
        <i class="fa-solid fa-stopwatch"></i>
        <div>
            <h2><?= $rataMenit !== null ? $rataMenit . ' mnt' : '-' ?></h2>
            <p>Rata-rata Daftar → Selesai</p>
        </div>
    </div>
</div>

<div class="chart-grid">
    <div class="table-section chart-wide">
        <h4><i class="fa-solid fa-chart-line"></i> Pasien per Hari (7 Hari Terakhir)</h4>
        <div class="chart-box"><canvas id="chartHari"></canvas></div>
    </div>

    <div class="table-section">
        <h4><i class="fa-solid fa-chart-pie"></i> Komposisi Kategori (30 Hari)</h4>
        <div class="chart-box"><canvas id="chartKategori"></canvas></div>
    </div>

    <div class="table-section">
        <h4><i class="fa-solid fa-clock"></i> Jam Tersibuk (30 Hari)</h4>
        <div class="chart-box"><canvas id="chartJam"></canvas></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const hijau = '#2e7d32';

    new Chart(document.getElementById('chartHari'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelHari) ?>,
            datasets: [{
                label: 'Pasien',
                data: <?= json_encode($dataHari) ?>,
                borderColor: hijau,
                backgroundColor: 'rgba(46,125,50,0.12)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        data: {
            labels: ['Darurat', 'Prioritas', 'Reguler'],
            datasets: [{
                data: <?= json_encode($dataKategori) ?>,
                backgroundColor: ['#c62828', '#ef6c00', '#607d8b']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    new Chart(document.getElementById('chartJam'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelJam) ?>,
            datasets: [{ label: 'Pasien', data: <?= json_encode($dataJam) ?>, backgroundColor: hijau }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

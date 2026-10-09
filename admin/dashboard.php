<?php
$pageTitle = 'Dashboard';
include 'includes/header.php';

// Query ringkasan data buat ditampilin di card
$totalHariIni  = $pdo->query("SELECT COUNT(*) FROM pasien WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$totalMenunggu = $pdo->query("SELECT COUNT(*) FROM pasien WHERE status = 'menunggu'")->fetchColumn();
$totalDarurat  = $pdo->query("SELECT COUNT(*) FROM pasien WHERE status = 'menunggu' AND kategori = 'darurat'")->fetchColumn();
$totalSelesai  = $pdo->query("SELECT COUNT(*) FROM pasien WHERE status = 'selesai' AND DATE(created_at) = CURDATE()")->fetchColumn();
?>
<div style="background:#fff; padding:24px; border-radius:10px; margin-bottom:20px; box-shadow:0 1px 4px rgba(0,0,0,.08);">
    <h2 style="margin:0 0 6px;"><i class="fa-solid fa-circle-user"></i> Selamat datang, Admin!</h2>
    <p style="margin:0; color:#666;">Kelola data pasien dari halaman Pendaftaran.</p>
</div>
<div class="card-grid">
    <div class="summary-card">
        <i class="fa-solid fa-calendar-day"></i>
        <div>
            <h2><?= $totalHariIni ?></h2>
            <p>Pasien Daftar Hari Ini</p>
        </div>
    </div>

    <div class="summary-card">
        <i class="fa-solid fa-hourglass-half"></i>
        <div>
            <h2><?= $totalMenunggu ?></h2>
            <p>Sedang Menunggu</p>
        </div>
    </div>

    <div class="summary-card danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <h2><?= $totalDarurat ?></h2>
            <p>Kategori Darurat Menunggu</p>
        </div>
    </div>

    <div class="summary-card success">
        <i class="fa-solid fa-circle-check"></i>
        <div>
            <h2><?= $totalSelesai ?></h2>
            <p>Selesai Dilayani Hari Ini</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
// ambil nama file halaman yang lagi aktif, buat kasih class "active" di menu
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-hospital"></i>
        <span>Antrian Pasien</span>
    </div>
    <nav class="sidebar-menu">
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="pendaftaran.php" class="<?= $currentPage === 'pendaftaran.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-clipboard-list"></i> Pendaftaran & Antrian
        </a>
        <a href="laporan.php" class="<?= $currentPage === 'laporan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i> Laporan
        </a>
    </nav>
</div>
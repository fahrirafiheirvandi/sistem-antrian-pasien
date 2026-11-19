<?php
// ambil nama file halaman yang lagi aktif, buat kasih class "active" di menu
$currentPage = basename($_SERVER['PHP_SELF']);

// daftar halaman per grup, dipakai buat nentuin dropdown mana yang kebuka otomatis
$grupPelayanan  = ['pendaftaran.php', 'edit_pasien.php'];
$grupData       = ['data_pasien.php', 'laporan.php', 'statistik.php'];
$grupPengaturan = ['pengguna.php', 'ganti_password.php'];
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-hospital"></i>
        <span>Antrian Pasien</span>
    </div>

    <nav class="sidebar-menu">

        <!-- Menu tunggal -->
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>

        <!-- Dropdown: Pelayanan -->
        <div class="menu-group <?= in_array($currentPage, $grupPelayanan) ? 'open' : '' ?>">
            <button type="button" class="menu-toggle">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Pelayanan</span>
                <i class="fa-solid fa-chevron-down menu-arrow"></i>
            </button>
            <div class="submenu">
                <a href="pendaftaran.php" class="<?= in_array($currentPage, $grupPelayanan) ? 'active' : '' ?>">
                    <i class="fa-solid fa-clipboard-list"></i> Pendaftaran & Antrian
                </a>
                <a href="../layar-antrian.php" target="_blank" rel="noopener">
                    <i class="fa-solid fa-tv"></i> Layar Antrian
                    <i class="fa-solid fa-arrow-up-right-from-square menu-external"></i>
                </a>
            </div>
        </div>

        <!-- Dropdown: Data & Laporan -->
        <div class="menu-group <?= in_array($currentPage, $grupData) ? 'open' : '' ?>">
            <button type="button" class="menu-toggle">
                <i class="fa-solid fa-folder-open"></i>
                <span>Data & Laporan</span>
                <i class="fa-solid fa-chevron-down menu-arrow"></i>
            </button>
            <div class="submenu">
                <a href="data_pasien.php" class="<?= $currentPage === 'data_pasien.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Data Pasien
                </a>
                <a href="laporan.php" class="<?= $currentPage === 'laporan.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-lines"></i> Laporan
                </a>
                <a href="statistik.php" class="<?= $currentPage === 'statistik.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-column"></i> Statistik
                </a>
            </div>
        </div>

        <!-- Dropdown: Pengaturan -->
        <div class="menu-group <?= in_array($currentPage, $grupPengaturan) ? 'open' : '' ?>">
            <button type="button" class="menu-toggle">
                <i class="fa-solid fa-gear"></i>
                <span>Pengaturan</span>
                <i class="fa-solid fa-chevron-down menu-arrow"></i>
            </button>
            <div class="submenu">
            <?php if ($isSuperadmin): ?>    
            <a href="pengguna.php" class="<?= $currentPage === 'pengguna.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-gear"></i> Kelola Pengguna
                </a>
            <?php endif; ?>    
                <a href="ganti_password.php" class="<?= $currentPage === 'ganti_password.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-key"></i> Ganti Password
                </a>
            </div>
        </div>

    </nav>
</div>

<script>
    // klik judul grup -> buka/tutup submenu
    document.querySelectorAll('.menu-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.parentElement.classList.toggle('open');
        });
    });
</script>
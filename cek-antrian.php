<?php
require_once 'config/database.php';
require_once 'includes/fungsi_antrian.php';

$tiket    = trim($_GET['tiket'] ?? '');
$notFound = false;
$pasien   = null;
$posisi   = null;

if ($tiket !== '') {
    $hasil = ambilStatusPasien($pdo, $tiket);

    if (!$hasil['found']) {
        $notFound = true;
    } else {
        $pasien = $hasil['pasien'];
        $posisi = $hasil['posisi'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Antrian Pasien</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/pasien.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="center-page">

    <div class="phone-container">

        <div class="phone-header">
            <i class="fa-solid fa-hospital"></i>
            <h2>Cek Antrian Pasien</h2>
            <p>Masukkan nomor tiket yang diberikan petugas</p>
        </div>

        <form action="cek-antrian.php" method="GET" class="cari-tiket-form">
            <input
                type="text"
                name="tiket"
                placeholder="Contoh: TKT-20260926-001"
                value="<?= htmlspecialchars($tiket) ?>"
                required
            >
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <?php if ($notFound): ?>
            <div class="alert-notfound">
                <i class="fa-solid fa-circle-exclamation"></i>
                Nomor tiket tidak ditemukan. Coba periksa kembali penulisannya.
            </div>

        <?php elseif ($pasien): ?>
            <div class="hasil-card">
                <div class="no-tiket"><?= htmlspecialchars($pasien['no_tiket']) ?></div>
                <div class="nama-pasien"><?= htmlspecialchars($pasien['nama']) ?></div>

                <span class="badge-kategori <?= $pasien['kategori'] ?>">
                    <?= ucfirst($pasien['kategori']) ?>
                </span>

                <!-- Kotak status ini isinya bakal di-update otomatis pake JS tiap 5 detik,
                     gak perlu refresh manual. Nilai awal (pas halaman pertama dibuka) dari PHP dulu. -->
                <div class="status-box <?= $pasien['status'] ?>" id="statusBox">
                    <i class="<?= $pasien['status'] === 'menunggu' ? 'fa-solid fa-hourglass-half' : ($pasien['status'] === 'dipanggil' ? 'fa-solid fa-bell' : 'fa-solid fa-circle-check') ?>" id="statusIcon"></i>

                    <div class="posisi-angka" id="posisiAngka" style="<?= $pasien['status'] !== 'menunggu' ? 'display:none;' : '' ?>">
                        <?= $posisi ?>
                    </div>

                    <div class="status-label" id="statusLabel">
                        <?php if ($pasien['status'] === 'menunggu'): ?>
                            <?= $posisi == 1 ? 'Anda antrian berikutnya' : "Ada $posisi pasien di depan Anda (termasuk Anda)" ?>
                        <?php elseif ($pasien['status'] === 'dipanggil'): ?>
                            Anda sedang dipanggil, silakan menuju ruang periksa.
                        <?php else: ?>
                            Anda sudah selesai dilayani. Terima kasih.
                        <?php endif; ?>
                    </div>
                </div>

                <p class="live-indicator" id="liveIndicator">
                    <i class="fa-solid fa-circle"></i> Update otomatis tiap 5 detik
                </p>
            </div>

            <a href="cek-antrian.php" class="cari-lagi">
                <i class="fa-solid fa-rotate-left"></i> Cek tiket lain
            </a>

            <script>
                const tiketAktif = "<?= addslashes($pasien['no_tiket']) ?>";
                const statusAwal = "<?= $pasien['status'] ?>";

                // kalau statusnya udah "selesai" dari awal, gak perlu polling sama sekali
                if (statusAwal !== 'selesai') {
                    const pollInterval = setInterval(refreshStatus, 5000);
                }

                function refreshStatus() {
                    fetch('api/cek_status.php?tiket=' + encodeURIComponent(tiketAktif))
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data.found) return;

                            const statusBox   = document.getElementById('statusBox');
                            const statusIcon  = document.getElementById('statusIcon');
                            const posisiAngka = document.getElementById('posisiAngka');
                            const statusLabel = document.getElementById('statusLabel');

                            statusBox.className = 'status-box ' + data.status;

                            if (data.status === 'menunggu') {
                                statusIcon.className = 'fa-solid fa-hourglass-half';
                                posisiAngka.style.display = 'block';
                                posisiAngka.textContent = data.posisi;
                                statusLabel.textContent = data.posisi == 1
                                    ? 'Anda antrian berikutnya'
                                    : 'Ada ' + data.posisi + ' pasien di depan Anda (termasuk Anda)';

                            } else if (data.status === 'dipanggil') {
                                statusIcon.className = 'fa-solid fa-bell';
                                posisiAngka.style.display = 'none';
                                statusLabel.textContent = 'Anda sedang dipanggil, silakan menuju ruang periksa.';

                            } else {
                                statusIcon.className = 'fa-solid fa-circle-check';
                                posisiAngka.style.display = 'none';
                                statusLabel.textContent = 'Anda sudah selesai dilayani. Terima kasih.';

                                // udah selesai, gak perlu polling lagi
                                document.getElementById('liveIndicator').style.display = 'none';
                                clearInterval(pollInterval);
                            }
                        })
                        .catch(function (err) {
                            console.error('Gagal ambil status terbaru:', err);
                        });
                }
            </script>
        <?php endif; ?>

    </div>

</body>
</html>
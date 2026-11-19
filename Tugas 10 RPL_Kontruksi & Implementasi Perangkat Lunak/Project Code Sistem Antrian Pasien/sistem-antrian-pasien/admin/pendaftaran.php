<?php
$pageTitle = 'Pendaftaran & Antrian Pasien';
include 'includes/header.php';

// pesan sukses dari proses/tambah_pasien.php, update_pasien.php, atau hapus_pasien.php
$sukses = $_SESSION['sukses'] ?? '';
unset($_SESSION['sukses']);

// data pasien yang baru aja didaftarkan (buat auto-munculin QR code-nya)
$newTiket = $_SESSION['new_tiket'] ?? '';
$newNama  = $_SESSION['new_nama'] ?? '';
unset($_SESSION['new_tiket'], $_SESSION['new_nama']);

// keyword pencarian dari form search (kalau ada)
$cari = trim($_GET['cari'] ?? '');

// query dasar: pasien yang statusnya masih aktif (menunggu / dipanggil)
// kalau ada keyword pencarian, tambahin filter WHERE nama atau no_tiket-nya cocok
if ($cari !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM pasien
        WHERE status IN ('menunggu', 'dipanggil')
        AND (nama LIKE ? OR no_tiket LIKE ?)
        ORDER BY FIELD(kategori, 'darurat', 'prioritas', 'reguler'), created_at ASC
    ");
    $keyword = "%$cari%";
    $stmt->execute([$keyword, $keyword]);
} else {
    $stmt = $pdo->query("
        SELECT * FROM pasien
        WHERE status IN ('menunggu', 'dipanggil')
        ORDER BY FIELD(kategori, 'darurat', 'prioritas', 'reguler'), created_at ASC
    ");
}
$daftarPasien = $stmt->fetchAll();
?>

<?php if ($sukses): ?>
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($sukses) ?></div>
<?php endif; ?>

<div class="form-section">
    <h4><i class="fa-solid fa-user-plus"></i> Tambah Pasien Baru</h4>
    <form action="proses/tambah_pasien.php" method="POST" class="form-grid">
        <div class="form-group">
            <label>Nama Pasien</label>
            <input type="text" name="nama" required>
        </div>
        <div class="form-group">
            <label>Usia</label>
            <input type="number" name="usia" min="0" required>
        </div>
        <div class="form-group">
            <label>No. HP</label>
            <input type="text" name="no_hp">
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" required>
                <option value="reguler">Reguler</option>
                <option value="prioritas">Prioritas</option>
                <option value="darurat">Darurat</option>
            </select>
        </div>
        <div class="form-group full">
            <label>Keluhan</label>
            <textarea name="keluhan" rows="2"></textarea>
        </div>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan & Daftarkan</button>
    </form>
</div>

<div class="table-section">
    <div class="table-header">
        <h4><i class="fa-solid fa-list-ol"></i> Daftar Antrian Aktif</h4>

        <form method="GET" class="search-form">
            <input type="text" name="cari" placeholder="Cari nama / no. tiket..." value="<?= htmlspecialchars($cari) ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            <?php if ($cari !== ''): ?>
                <a href="pendaftaran.php" class="btn-reset" title="Reset pencarian"><i class="fa-solid fa-xmark"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <table class="table-antrian">
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Nama</th>
                <th>Usia</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($daftarPasien)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">
                        <?= $cari !== '' ? 'Pasien dengan kata kunci "' . htmlspecialchars($cari) . '" tidak ditemukan.' : 'Belum ada pasien di antrian.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($daftarPasien as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['no_tiket']) ?></td>
                    <td><?= htmlspecialchars($p['nama']) ?></td>
                    <td><?= $p['usia'] ?></td>
                    <td><span class="badge badge-<?= $p['kategori'] ?>"><?= ucfirst($p['kategori']) ?></span></td>
                    <td><?= ucfirst($p['status']) ?></td>
                    <td>
                        <div class="aksi-group">
                            <?php if ($p['status'] === 'menunggu'): ?>
                                <a href="proses/update_status.php?id=<?= $p['id'] ?>&status=dipanggil"
                                   class="btn-aksi panggil js-confirm"
                                   data-title="Panggil Pasien"
                                   data-text="Panggil pasien <?= htmlspecialchars(addslashes($p['nama'])) ?> sekarang?"
                                   data-icon="question"
                                   data-confirm-text="Ya, Panggil"
                                   data-confirm-color="#ef6c00">
                                    <i class="fa-solid fa-bell"></i>
                                </a>
                            <?php elseif ($p['status'] === 'dipanggil'): ?>
                                <a href="proses/update_status.php?id=<?= $p['id'] ?>&status=selesai"
                                   class="btn-aksi selesai js-confirm"
                                   data-title="Selesai Dilayani"
                                   data-text="Tandai pasien <?= htmlspecialchars(addslashes($p['nama'])) ?> selesai dilayani?"
                                   data-icon="question"
                                   data-confirm-text="Ya, Selesai"
                                   data-confirm-color="#2e7d32">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            <?php endif; ?>

                            <a href="edit_pasien.php?id=<?= $p['id'] ?>" class="btn-aksi edit" title="Edit data">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="#"
                               class="btn-aksi qr btn-lihat-qr"
                               title="Lihat QR Code"
                               data-tiket="<?= htmlspecialchars($p['no_tiket']) ?>"
                               data-nama="<?= htmlspecialchars($p['nama']) ?>">
                                <i class="fa-solid fa-qrcode"></i>
                            </a>

                            <a href="proses/hapus_pasien.php?id=<?= $p['id'] ?>"
                               class="btn-aksi hapus js-confirm"
                               title="Hapus data"
                               data-title="Hapus Data Pasien"
                               data-text="Data pasien <?= htmlspecialchars(addslashes($p['nama'])) ?> akan dihapus permanen dan tidak bisa dikembalikan."
                               data-icon="warning"
                               data-confirm-text="Ya, Hapus"
                               data-confirm-color="#c62828">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal QR Code -->
<div class="modal-overlay" id="qrModal">
    <div class="modal-box">
        <button type="button" class="modal-close" id="qrModalClose">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h4>QR Code Antrian</h4>
        <p id="qrModalNama" class="qr-nama"></p>
        <div id="qrcodeBox"></div>
        <p class="qr-hint">Tunjukkan/print QR ini ke pasien. Pasien tinggal scan buat cek posisi antrian dari HP-nya.</p>
    </div>
</div>

<script>
    const qrModal      = document.getElementById('qrModal');
    const qrModalClose = document.getElementById('qrModalClose');
    const qrModalNama  = document.getElementById('qrModalNama');
    const qrcodeBox    = document.getElementById('qrcodeBox');

    function tampilkanQR(tiket, nama) {
        qrcodeBox.innerHTML = ''; // bersihin QR sebelumnya

        // BASE_URL diambil dari config/config.php, tinggal ganti di 1 tempat
        // kalau mau demo dari HP lain (liat instruksi di file config.php)
        const urlCek = "<?= BASE_URL ?>/cek-antrian.php?tiket=" + encodeURIComponent(tiket);

        new QRCode(qrcodeBox, {
            text: urlCek,
            width: 180,
            height: 180
        });

        qrModalNama.textContent = nama + ' — ' + tiket;
        qrModal.classList.add('show');
    }

    // tombol "Lihat QR" di tiap baris tabel
    document.querySelectorAll('.btn-lihat-qr').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            tampilkanQR(this.dataset.tiket, this.dataset.nama);
        });
    });

    qrModalClose.addEventListener('click', function () {
        qrModal.classList.remove('show');
    });

    // auto muncul QR kalau baru aja selesai daftarin pasien baru
    <?php if ($newTiket): ?>
    tampilkanQR('<?= addslashes($newTiket) ?>', '<?= addslashes($newNama) ?>');
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
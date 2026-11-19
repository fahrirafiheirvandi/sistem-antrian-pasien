<?php
$pageTitle = 'Edit Data Pasien';
include 'includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: pendaftaran.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pasien WHERE id = ?");
$stmt->execute([$id]);
$pasien = $stmt->fetch();

// kalau id-nya gak ketemu di database, tendang balik ke halaman pendaftaran
if (!$pasien) {
    header('Location: pendaftaran.php');
    exit;
}
?>

<div class="form-section">
    <h4><i class="fa-solid fa-pen"></i> Edit Data Pasien — <?= htmlspecialchars($pasien['no_tiket']) ?></h4>

    <form action="proses/update_pasien.php" method="POST" class="form-grid">
        <input type="hidden" name="id" value="<?= $pasien['id'] ?>">

        <div class="form-group">
            <label>Nama Pasien</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($pasien['nama']) ?>" required>
        </div>

        <div class="form-group">
            <label>Usia</label>
            <input type="number" name="usia" min="0" value="<?= $pasien['usia'] ?>" required>
        </div>

        <div class="form-group">
            <label>No. HP</label>
            <input type="text" name="no_hp" value="<?= htmlspecialchars($pasien['no_hp']) ?>">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" required>
                <option value="reguler" <?= $pasien['kategori'] === 'reguler' ? 'selected' : '' ?>>Reguler</option>
                <option value="prioritas" <?= $pasien['kategori'] === 'prioritas' ? 'selected' : '' ?>>Prioritas</option>
                <option value="darurat" <?= $pasien['kategori'] === 'darurat' ? 'selected' : '' ?>>Darurat</option>
            </select>
        </div>

        <div class="form-group full">
            <label>Keluhan</label>
            <textarea name="keluhan" rows="2"><?= htmlspecialchars($pasien['keluhan']) ?></textarea>
        </div>

        <div class="form-actions full">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
            <a href="pendaftaran.php" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
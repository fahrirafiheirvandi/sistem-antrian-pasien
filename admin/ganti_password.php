<?php
$pageTitle = 'Ganti Password';
include 'includes/header.php';

$sukses = $_SESSION['sukses_password'] ?? '';
$error  = $_SESSION['error_password'] ?? '';
unset($_SESSION['sukses_password'], $_SESSION['error_password']);
?>

<div class="form-section" style="max-width: 450px;">
    <h4><i class="fa-solid fa-key"></i> Ganti Password</h4>

    <?php if ($sukses): ?>
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($sukses) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error-box"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="proses/ganti_password.php" method="POST" class="form-grid">
        <div class="form-group full">
            <label>Password Lama</label>
            <input type="password" name="password_lama" required>
        </div>
        <div class="form-group full">
            <label>Password Baru</label>
            <input type="password" name="password_baru" minlength="6" required>
        </div>
        <div class="form-group full">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi_password" minlength="6" required>
        </div>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Password Baru</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
<?php
$pageTitle = 'Kelola Pengguna';
$hanyaSuperadmin = true; // halaman ini khusus superadmin
include 'includes/header.php';

$sukses = $_SESSION['sukses_user'] ?? '';
$error  = $_SESSION['error_user'] ?? '';
unset($_SESSION['sukses_user'], $_SESSION['error_user']);

$users = $pdo->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY id ASC")->fetchAll();
?>

<?php if ($sukses): ?>
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($sukses) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-error-box"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="form-section">
    <h4><i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru</h4>
    <form action="proses/tambah_user.php" method="POST" class="form-grid">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" pattern="[A-Za-z0-9_.]{3,50}" title="3-50 karakter: huruf, angka, titik, atau underscore" required>
        </div>
        <div class="form-group full">
            <label>Password (minimal 6 karakter)</label>
            <input type="password" name="password" minlength="6" required>
        </div>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengguna</button>
    </form>
</div>

<div class="table-section">
    <h4><i class="fa-solid fa-user-gear"></i> Daftar Pengguna</h4>
    <table class="table-antrian">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <?php $isSaya = (int) $u['id'] === (int) $_SESSION['user_id']; ?>
            <tr>
                <td>
                    <?= htmlspecialchars($u['nama_lengkap']) ?>
                    <?php if ($isSaya): ?><span class="badge badge-saya">Anda</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <div class="aksi-group">
    <a href="#" class="btn-aksi edit btn-edit-user" title="Edit nama / username"
       data-id="<?= (int) $u['id'] ?>"
       data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>"
       data-username="<?= htmlspecialchars($u['username']) ?>">
        <i class="fa-solid fa-pen"></i>
    </a>

    <a href="#" class="btn-aksi edit btn-reset-pw" title="Reset password"
       data-id="<?= (int) $u['id'] ?>"
       data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>">
        <i class="fa-solid fa-key"></i>
    </a>

    <?php if (!$isSaya && $u['role'] !== 'superadmin'): ?>
    <a href="proses/hapus_user.php?id=<?= (int) $u['id'] ?>"
       class="btn-aksi hapus js-confirm"
       title="Hapus pengguna"
       data-title="Hapus Pengguna"
       data-text="Akun <?= htmlspecialchars(addslashes($u['username'])) ?> akan dihapus permanen."
       data-icon="warning"
       data-confirm-text="Ya, Hapus"
       data-confirm-color="#c62828">
        <i class="fa-solid fa-trash"></i>
    </a>
    <?php endif; ?>
</div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- form tersembunyi buat reset password, diisi lewat popup SweetAlert -->
<form id="formEditUser" action="proses/update_user.php" method="POST" style="display:none;">
    <input type="hidden" name="id" id="editUserId">
    <input type="hidden" name="nama_lengkap" id="editUserNama">
    <input type="hidden" name="username" id="editUserUsername">
</form>
<form id="formResetPw" action="proses/reset_password_user.php" method="POST" style="display:none;">
    <input type="hidden" name="id" id="resetPwId">
    <input type="hidden" name="password_baru" id="resetPwBaru">
</form>

<script>
document.querySelectorAll('.btn-edit-user').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const id       = this.dataset.id;
        const nama     = this.dataset.nama;
        const username = this.dataset.username;

        Swal.fire({
            title: 'Edit Pengguna',
            html: '<input id="swNama" class="swal2-input" placeholder="Nama lengkap">' +
                  '<input id="swUsername" class="swal2-input" placeholder="Username">',
            didOpen: function () {
                document.getElementById('swNama').value = nama;
                document.getElementById('swUsername').value = username;
            },
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2e7d32',
            cancelButtonColor: '#888888',
            reverseButtons: true,
            preConfirm: function () {
                const n = document.getElementById('swNama').value.trim();
                const u = document.getElementById('swUsername').value.trim();
                if (!n) {
                    Swal.showValidationMessage('Nama tidak boleh kosong.');
                    return false;
                }
                if (!/^[A-Za-z0-9_.]{3,50}$/.test(u)) {
                    Swal.showValidationMessage('Username 3-50 karakter: huruf, angka, titik, underscore.');
                    return false;
                }
                return { nama: n, username: u };
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('editUserId').value       = id;
                document.getElementById('editUserNama').value     = result.value.nama;
                document.getElementById('editUserUsername').value = result.value.username;
                document.getElementById('formEditUser').submit();
            }
        });
    });
});    
    document.querySelectorAll('.btn-reset-pw').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id   = this.dataset.id;
            const nama = this.dataset.nama;

            Swal.fire({
                title: 'Reset Password',
                text: 'Masukkan password baru untuk ' + nama,
                input: 'password',
                inputAttributes: { autocomplete: 'new-password' },
                inputPlaceholder: 'Minimal 6 karakter',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2e7d32',
                cancelButtonColor: '#888888',
                reverseButtons: true,
                inputValidator: function (v) {
                    if (!v || v.length < 6) return 'Password minimal 6 karakter.';
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('resetPwId').value   = id;
                    document.getElementById('resetPwBaru').value = result.value;
                    document.getElementById('formResetPw').submit();
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

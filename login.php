<?php
session_start();

// kalau udah login, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Sistem Antrian Pasien</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/vendor/fontawesome/css/all.min.css">
</head>
<body class="center-page">

    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-hospital"></i>
            <h2>Sistem Antrian Pasien</h2>
            <p>Silakan login sebagai admin untuk melanjutkan</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
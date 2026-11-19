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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Antrian Pasien</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/vendor/fontawesome/css/all.min.css">
</head>
<body class="lg-page">

    <div class="lg-card">

        <!-- Kolom kiri: ilustrasi + headline -->
        <div class="lg-left">
            <img src="images/login-ilustrasi.jpg" alt="Ilustrasi dokter dan pasien di ruang tunggu">
            <h2 class="lg-headline">
                Kelola Antrian Pasien dengan <span>Mudah dan Cepat!</span>
            </h2>
        </div>

        <!-- Kolom kanan: form login -->
        <div class="lg-right">
            <div class="lg-form-wrap">
                <div class="lg-brand">
                    <div class="lg-brand-icon"><i class="fa-solid fa-hospital"></i></div>
                    <h1>Sistem<br>Antrian Pasien</h1>
                </div>
                <p class="lg-sub">Silakan login sebagai admin untuk melanjutkan</p>

                <?php if ($error): ?>
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form action="login_process.php" method="POST">
                    <div class="lg-group">
                        <label for="username">Username</label>
                        <div class="lg-field">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>

                    <div class="lg-group">
                        <label for="password">Password</label>
                        <div class="lg-field">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                            <i class="fa-solid fa-eye" id="togglePassword"></i>
                        </div>
                    </div>

                    <button type="submit" class="lg-btn">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk
                    </button>
                </form>
            </div>

            <p class="lg-footer">Hak Cipta &copy; <?= date('Y') ?> Kelompok 6</p>
        </div>

    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput  = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>

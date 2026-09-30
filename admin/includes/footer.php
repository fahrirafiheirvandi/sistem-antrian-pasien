</div><!-- /.content-body -->
    </div><!-- /.main-content -->
</div><!-- /.wrapper -->

<script>
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    profileToggle.addEventListener('click', function () {
        profileDropdown.classList.toggle('show');
    });

    // biar dropdown nutup otomatis kalau user klik di luar area profile
    document.addEventListener('click', function (e) {
        if (!profileToggle.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    });

    // Handle semua link yang butuh konfirmasi (class="js-confirm")
    // pake SweetAlert2, gantiin confirm() bawaan browser yang tampilannya jelek
    document.querySelectorAll('.js-confirm').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const link = this.getAttribute('href');

            Swal.fire({
                title: this.dataset.title || 'Konfirmasi',
                text: this.dataset.text || 'Yakin mau lanjut?',
                icon: this.dataset.icon || 'question',
                showCancelButton: true,
                confirmButtonText: this.dataset.confirmText || 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: this.dataset.confirmColor || '#2e7d32',
                cancelButtonColor: '#888888',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });
    });
</script>
</body>
</html>
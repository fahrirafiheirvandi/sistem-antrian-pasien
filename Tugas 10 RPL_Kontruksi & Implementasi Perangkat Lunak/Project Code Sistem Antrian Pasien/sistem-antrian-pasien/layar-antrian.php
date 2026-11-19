<?php
// Halaman publik buat ditampilin di TV / monitor ruang tunggu.
// Dibuka dari menu "Layar Antrian" di sidebar admin, terus tekan F11 buat fullscreen.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layar.css">
    <link rel="stylesheet" href="css/vendor/fontawesome/css/all.min.css">
</head>
<body class="layar-body">

    <header class="layar-header">
        <div class="layar-brand"><i class="fa-solid fa-hospital"></i> Antrian Pasien</div>
        <div class="layar-jam" id="jam">--:--:--</div>
    </header>

    <main class="layar-main">
        <section class="layar-panggil">
            <div class="layar-label">SEDANG DIPANGGIL</div>
            <div class="layar-nomor" id="nomorUtama">-</div>
            <div class="layar-nama" id="namaUtama">Belum ada panggilan</div>
            <div class="layar-sebelumnya" id="sebelumnya"></div>
        </section>

        <aside class="layar-berikut">
            <div class="layar-label">ANTRIAN BERIKUTNYA</div>
            <ul id="daftarMenunggu"></ul>
            <div class="layar-total" id="totalMenunggu"></div>
        </aside>
    </main>

    <button type="button" class="layar-suara" id="btnSuara">
        <i class="fa-solid fa-volume-xmark"></i> <span>Suara mati</span>
    </button>

<script>
    const elNomor = document.getElementById('nomorUtama');
    const elNama  = document.getElementById('namaUtama');
    const elSebelumnya = document.getElementById('sebelumnya');
    const elDaftar = document.getElementById('daftarMenunggu');
    const elTotal  = document.getElementById('totalMenunggu');
    const btnSuara = document.getElementById('btnSuara');

    let suaraAktif = false;
    let tiketTerakhir = null; // buat deteksi ada panggilan baru

    // ---------- jam ----------
    function updateJam() {
        document.getElementById('jam').textContent = new Date().toLocaleTimeString('id-ID');
    }
    setInterval(updateJam, 1000);
    updateJam();

    // ---------- suara panggilan ----------
    // browser nge-blok suara otomatis, jadi harus diaktifin manual dulu lewat klik tombol
    btnSuara.addEventListener('click', function () {
        suaraAktif = !suaraAktif;
        this.querySelector('i').className = suaraAktif ? 'fa-solid fa-volume-high' : 'fa-solid fa-volume-xmark';
        this.querySelector('span').textContent = suaraAktif ? 'Suara aktif' : 'Suara mati';
        if (suaraAktif) umumkan('Suara antrian diaktifkan');
    });

    function umumkan(teks) {
        if (!suaraAktif || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(teks);
        u.lang = 'id-ID';
        u.rate = 0.9;
        window.speechSynthesis.speak(u);
    }

    // "TKT-20260926-003" -> "nomor antrian tiga" (ambil bagian urutan di belakang saja)
    function bacaTiket(tiket) {
        const urutan = parseInt(tiket.split('-').pop(), 10);
        return 'Nomor antrian ' + (isNaN(urutan) ? tiket : urutan);
    }

    // "TKT-20260926-003" -> "003" biar tampil besar & gampang dibaca dari jauh
    function nomorPendek(tiket) {
        return tiket.split('-').pop();
    }

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    // ---------- ambil data & render ----------
    async function muat() {
        try {
            const res  = await fetch('api/layar_antrian.php', { cache: 'no-store' });
            const data = await res.json();
            render(data);
        } catch (e) {
            // koneksi putus sebentar, abaikan aja, nanti dicoba lagi di interval berikutnya
        }
    }

    function render(data) {
        const utama = data.dipanggil[0];

        if (utama) {
            elNomor.textContent = nomorPendek(utama.tiket);
            elNama.textContent  = utama.nama;

            // kalau tiket yang dipanggil beda dari sebelumnya -> ada panggilan baru
            if (tiketTerakhir !== null && utama.tiket !== tiketTerakhir) {
                elNomor.classList.remove('kedip');
                void elNomor.offsetWidth; // trik biar animasi bisa diulang
                elNomor.classList.add('kedip');
                umumkan(bacaTiket(utama.tiket) + ', silakan menuju ruang periksa');
            }
            tiketTerakhir = utama.tiket;
        } else {
            elNomor.textContent = '-';
            elNama.textContent  = 'Belum ada panggilan';
            tiketTerakhir = tiketTerakhir === null ? '' : tiketTerakhir;
        }

        // panggilan sebelumnya (maksimal 2)
        const lalu = data.dipanggil.slice(1);
        elSebelumnya.innerHTML = lalu.length
            ? 'Sebelumnya: ' + lalu.map(p => '<b>' + esc(nomorPendek(p.tiket)) + '</b>').join(', ')
            : '';

        // daftar berikutnya
        elDaftar.innerHTML = data.menunggu.length
            ? data.menunggu.map(p =>
                '<li><span class="no">' + esc(nomorPendek(p.tiket)) + '</span>' +
                '<span class="nm">' + esc(p.nama) + '</span>' +
                '<span class="badge badge-' + esc(p.kategori) + '">' + esc(p.kategori) + '</span></li>'
              ).join('')
            : '<li class="kosong">Tidak ada antrian</li>';

        elTotal.textContent = data.jumlah_menunggu + ' pasien menunggu';
    }

    muat();
    setInterval(muat, 5000); // refresh tiap 5 detik
</script>
</body>
</html>

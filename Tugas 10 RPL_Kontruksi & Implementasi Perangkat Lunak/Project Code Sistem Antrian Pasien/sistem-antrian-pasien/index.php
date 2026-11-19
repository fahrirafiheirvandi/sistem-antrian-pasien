<?php
// File ini sengaja isinya cuma redirect doang.
// Fungsinya biar kalau orang akses folder root (tanpa nyebut nama file),
// otomatis dilempar ke halaman login, bukan nampilin daftar isi folder.
header('Location: login.php');
exit;
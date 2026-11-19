<?php
$pageTitle = 'Data Pasien';
include 'includes/header.php';

// ---------- ambil filter dari URL ----------
$cari     = trim($_GET['cari'] ?? '');
$status   = $_GET['status'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$dari     = $_GET['dari'] ?? '';
$sampai   = $_GET['sampai'] ?? '';

// whitelist biar nilai dari URL gak sembarangan
if (!in_array($status, ['menunggu', 'dipanggil', 'selesai'], true)) $status = '';
if (!in_array($kategori, ['darurat', 'prioritas', 'reguler'], true)) $kategori = '';

// ---------- susun WHERE secara dinamis (tetap pake prepared statement) ----------
$where  = [];
$params = [];

if ($cari !== '') {
    $where[]  = '(nama LIKE ? OR no_tiket LIKE ? OR no_hp LIKE ?)';
    $kw       = "%$cari%";
    array_push($params, $kw, $kw, $kw);
}
if ($status !== '')   { $where[] = 'status = ?';   $params[] = $status; }
if ($kategori !== '') { $where[] = 'kategori = ?'; $params[] = $kategori; }
if ($dari !== '')     { $where[] = 'DATE(created_at) >= ?'; $params[] = $dari; }
if ($sampai !== '')   { $where[] = 'DATE(created_at) <= ?'; $params[] = $sampai; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ---------- pagination ----------
$perHalaman = 15;
$halaman    = max(1, (int) ($_GET['hal'] ?? 1));

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pasien $whereSql");
$stmt->execute($params);
$totalData  = (int) $stmt->fetchColumn();
$totalHal   = max(1, (int) ceil($totalData / $perHalaman));
$halaman    = min($halaman, $totalHal);
$offset     = ($halaman - 1) * $perHalaman;

// LIMIT/OFFSET sudah pasti integer (hasil casting di atas), jadi aman ditaro langsung
$stmt = $pdo->prepare("SELECT * FROM pasien $whereSql ORDER BY created_at DESC LIMIT $perHalaman OFFSET $offset");
$stmt->execute($params);
$daftar = $stmt->fetchAll();

// query string buat link pagination & tombol export (tanpa parameter "hal")
$qs = http_build_query(array_filter([
    'cari' => $cari, 'status' => $status, 'kategori' => $kategori,
    'dari' => $dari, 'sampai' => $sampai,
], fn($v) => $v !== ''));
$qsPrefix = $qs ? $qs . '&' : '';
?>

<div class="filter-section">
    <form method="GET" class="filter-form wrap">
        <input type="text" name="cari" placeholder="Cari nama / tiket / no. HP..." value="<?= htmlspecialchars($cari) ?>">

        <select name="status">
            <option value="">Semua Status</option>
            <?php foreach (['menunggu', 'dipanggil', 'selesai'] as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="kategori">
            <option value="">Semua Kategori</option>
            <?php foreach (['darurat', 'prioritas', 'reguler'] as $k): ?>
                <option value="<?= $k ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= ucfirst($k) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Dari</label>
        <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
        <label>Sampai</label>
        <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">

        <button type="submit" class="btn-submit"><i class="fa-solid fa-filter"></i> Terapkan</button>
        <?php if ($qs): ?>
            <a href="data_pasien.php" class="btn-cancel">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-section">
    <div class="table-header">
        <h4><i class="fa-solid fa-users"></i> Semua Data Pasien <small class="muted">(<?= $totalData ?> data)</small></h4>
        <a href="export_pasien.php?<?= htmlspecialchars($qs) ?>" class="btn-export">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
    </div>

    <div class="table-scroll">
    <table class="table-antrian">
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Nama</th>
                <th>Usia</th>
                <th>No. HP</th>
                <th>Kategori</th>
                <th>Keluhan</th>
                <th>Status</th>
                <th>Waktu Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="8" style="text-align:center;">Tidak ada data yang cocok dengan filter.</td></tr>
            <?php else: ?>
                <?php foreach ($daftar as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['no_tiket']) ?></td>
                    <td><?= htmlspecialchars($p['nama']) ?></td>
                    <td><?= (int) $p['usia'] ?></td>
                    <td><?= htmlspecialchars($p['no_hp'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $p['kategori'] ?>"><?= ucfirst($p['kategori']) ?></span></td>
                    <td><?= htmlspecialchars($p['keluhan'] ?? '-') ?></td>
                    <td><span class="badge badge-status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

    <?php if ($totalHal > 1): ?>
    <div class="pagination">
        <?php if ($halaman > 1): ?>
            <a href="?<?= $qsPrefix ?>hal=<?= $halaman - 1 ?>"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $halaman - 2); $i <= min($totalHal, $halaman + 2); $i++): ?>
            <a href="?<?= $qsPrefix ?>hal=<?= $i ?>" class="<?= $i === $halaman ? 'current' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($halaman < $totalHal): ?>
            <a href="?<?= $qsPrefix ?>hal=<?= $halaman + 1 ?>"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

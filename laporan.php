<?php
$page = 'laporan';
$title = 'Laporan';

require_once 'db/functions.php';
require_once 'components/header.php';

// Inisialisasi variabel untuk filtering
$data = [];
$jenis = "";
$tgl_mulai = "";
$tgl_selesai = "";

// logic filter
if (isset($_POST['tampilkan'])) {
    $jenis = $_POST['jenis_laporan'] ?? '';
    $tgl_mulai = $_POST['tgl_mulai'] ?? '';
    $tgl_selesai = $_POST['tgl_selesai'] ?? '';

    if ($jenis === 'stok') {
        // Laporan Stok saat ini
        $data = query("SELECT barang.*, kategori.nama_kategori 
                       FROM barang 
                       LEFT JOIN kategori ON barang.kategori_id = kategori.id");
    } elseif ($jenis === 'masuk' && $tgl_mulai !== '' && $tgl_selesai !== '') {
        // Laporan Barang Masuk per Tanggal dengan prepared statements
        $data = query("SELECT bm.*, b.nama_barang 
                       FROM barang_masuk bm 
                       JOIN barang b ON bm.barang_id = b.id 
                       WHERE bm.tanggal BETWEEN ? AND ?", [$tgl_mulai, $tgl_selesai]);
    } elseif ($jenis === 'keluar' && $tgl_mulai !== '' && $tgl_selesai !== '') {
        // Laporan Barang Keluar per Tanggal dengan prepared statements
        $data = query("SELECT bk.*, b.nama_barang 
                       FROM barang_keluar bk 
                       JOIN barang b ON bk.barang_id = b.id 
                       WHERE bk.tanggal BETWEEN ? AND ?", [$tgl_mulai, $tgl_selesai]);
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            <h1 class="h2 mb-4">Laporan Inventaris</h1>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jenis Laporan</label>
                                <select name="jenis_laporan" id="jenis_laporan" class="form-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="stok" <?= ($jenis === 'stok') ? 'selected' : ''; ?>>Stok Barang Saat Ini</option>
                                    <option value="masuk" <?= ($jenis === 'masuk') ? 'selected' : ''; ?>>Barang Masuk</option>
                                    <option value="keluar" <?= ($jenis === 'keluar') ? 'selected' : ''; ?>>Barang Keluar</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3" id="input-tgl-mulai">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" class="form-control" value="<?= esc($tgl_mulai); ?>">
                            </div>
                            <div class="col-md-3 mb-3" id="input-tgl-selesai">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" class="form-control" value="<?= esc($tgl_selesai); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" name="tampilkan" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($_POST['tampilkan']) && !empty($data)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">Hasil Laporan</h5>
                        
                        <a href="cetak_laporan.php?jenis=<?= esc($jenis); ?>&tgl_mulai=<?= esc($tgl_mulai); ?>&tgl_selesai=<?= esc($tgl_selesai); ?>" target="_blank" class="btn btn-sm btn-danger">
                            <i class="bi bi-printer"></i> Cetak PDF / Print
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <?php if ($jenis === 'stok'): ?>
                                            <th>Nama Barang</th>
                                            <th>Kategori</th>
                                            <th>Stok</th>
                                            <th>Harga</th>
                                        <?php else: ?>
                                            <th>Tanggal</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($data as $row): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        
                                        <?php if ($jenis === 'stok'): ?>
                                            <td><?= esc($row['nama_barang']); ?></td>
                                            <td><?= esc($row['nama_kategori'] ?? 'Tidak ada Kategori'); ?></td>
                                            <td><?= esc($row['stok']); ?></td>
                                            <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
                                        <?php else: ?>
                                            <td><?= esc(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                                            <td><?= esc($row['nama_barang']); ?></td>
                                            <td><?= esc($row['jumlah']); ?></td>
                                            <td><?= esc($row['keterangan'] ?? ''); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_POST['tampilkan']) && empty($data)): ?>
                <div class="alert alert-warning">Data tidak ditemukan pada periode tersebut.</div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>

<script>
    const selectJenis = document.getElementById('jenis_laporan');
    const inputMulai = document.querySelector('input[name="tgl_mulai"]');
    const inputSelesai = document.querySelector('input[name="tgl_selesai"]');

    selectJenis.addEventListener('change', function() {
        if (this.value === 'stok') {
            inputMulai.disabled = true;
            inputSelesai.disabled = true;
        } else {
            inputMulai.disabled = false;
            inputSelesai.disabled = false;
        }
    });
</script>
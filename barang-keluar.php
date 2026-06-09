<?php
$page = 'barang-keluar'; // Sidebar Active
$title = 'Barang Keluar';

require_once 'db/functions.php';
require_once 'components/header.php';

// Ambil data Barang Keluar JOIN Barang
$transaksi = query("SELECT bk.*, b.nama_barang 
                    FROM barang_keluar bk 
                    JOIN barang b ON bk.barang_id = b.id 
                    ORDER BY bk.tanggal DESC, bk.id DESC");

$barang = query("SELECT * FROM barang");
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Barang Keluar</h1>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalKeluar">
                    <i class="bi bi-dash-lg"></i> Input Barang Keluar
                </button>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'stok_kurang'): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Gagal!</strong> Stok barang tidak mencukupi untuk transaksi ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Status: <?= $_GET['status']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Keluar</th>
                                    <th>Keterangan</th>
                                    <?php if(isAdmin()): ?>
                                    <th>Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($transaksi as $row) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                                    <td><?= esc($row['nama_barang']); ?></td>
                                    <td class="text-danger fw-bold">- <?= esc($row['jumlah']); ?></td>
                                    <td><?= esc($row['keterangan'] ?? ''); ?></td>
                                    <?php if(isAdmin()): ?>
                                    <td>
                                        <a href="proses_transaksi.php?hapus_keluar=<?= esc($row['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus? Stok akan bertambah kembali.')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="modalKeluar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Input Barang Keluar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_transaksi.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang</label>
                        <select name="barang_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b) : ?>
                                <option value="<?= $b['id']; ?>"><?= $b['nama_barang']; ?> (Stok: <?= $b['stok']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Keluar</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Tujuan/Alasan)</label>
                        <textarea name="keterangan" class="form-control" placeholder="Contoh: Rusak / Terjual"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah_keluar" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>
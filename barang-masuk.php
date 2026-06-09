<?php
$page = 'barang-masuk'; // Sidebar Active
$title = 'Barang Masuk';

require_once 'db/functions.php';
require_once 'components/header.php';

// Ambil data Barang Masuk JOIN Barang
$transaksi = query("SELECT bm.*, b.nama_barang 
                    FROM barang_masuk bm 
                    JOIN barang b ON bm.barang_id = b.id 
                    ORDER BY bm.tanggal DESC, bm.id DESC");

// Ambil data Barang untuk Dropdown Form
$barang = query("SELECT * FROM barang");
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Barang Masuk</h1>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk">
                    <i class="bi bi-plus-lg"></i> Input Barang Masuk
                </button>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Status: <?= $_GET['status']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
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
                                    <th>Jumlah Masuk</th>
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
                                    <td class="text-success fw-bold">+ <?= esc($row['jumlah']); ?></td>
                                    <td><?= esc($row['keterangan'] ?? ''); ?></td>
                                    <?php if(isAdmin()): ?>
                                    <td>
                                        <a href="proses_transaksi.php?hapus_masuk=<?= esc($row['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus? Stok akan berkurang otomatis.')">
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

<div class="modal fade" id="modalMasuk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Input Barang Masuk</h5>
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
                        <label class="form-label">Jumlah Masuk</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Supplier/Sumber)</label>
                        <textarea name="keterangan" class="form-control" placeholder="Contoh: Dari Supplier A"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah_masuk" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>
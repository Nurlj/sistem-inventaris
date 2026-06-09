<?php
$page = 'barang';
$title = 'Data Barang';

require_once 'db/functions.php';
require_once 'components/header.php';

// 1. LOGIKA PENCARIAN & PAGINATION
$jumlahDataPerHalaman = 20;
$halamanAktif = (int)($_GET['halaman'] ?? 1);
if ($halamanAktif < 1) {
    $halamanAktif = 1;
}
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

// Cek apakah user sedang mencari sesuatu?
$keyword = $_GET['cari'] ?? '';
$params = [];

if ($keyword !== '') {
    // Query Dasar Pencarian dengan placeholders (?) untuk prepared statement
    $baseQuery = "SELECT barang.*, kategori.nama_kategori 
                  FROM barang 
                  LEFT JOIN kategori ON barang.kategori_id = kategori.id 
                  WHERE nama_barang LIKE ? OR barang.id = ?";
    $likeKeyword = "%$keyword%";
    $params = [$likeKeyword, $keyword];
} else {
    // Query Dasar Normal (Tanpa Pencarian)
    $baseQuery = "SELECT barang.*, kategori.nama_kategori 
                  FROM barang 
                  LEFT JOIN kategori ON barang.kategori_id = kategori.id";
}

// 2. Hitung Total Data (Menggunakan query() yang baru yang telah dimodernisasi dengan prepared statements)
$allData = query($baseQuery, $params); 
$jumlahData = count($allData);

// 3. Hitung Jumlah Halaman
$jumlahHalaman = (int)ceil($jumlahData / $jumlahDataPerHalaman);

// 4. Ambil Data Final dengan LIMIT
$finalQuery = $baseQuery . " ORDER BY barang.id DESC LIMIT ?, ?";
$paramsPagination = [...$params, $awalData, $jumlahDataPerHalaman];
$barang = query($finalQuery, $paramsPagination);

// Fetch Data Kategori untuk Dropdown Modal Tambah/Edit
$kategori = query("SELECT * FROM kategori");
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Barang</h1>
    
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="cari" class="form-control form-control-sm me-2" placeholder="Cari Nama / ID..." value="<?= esc($keyword); ?>" size="30">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($keyword !== ''): ?>
                            <a href="data-barang.php" class="btn btn-sm btn-outline-danger ms-1" title="Reset Pencarian">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>

                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Status: <?= esc($_GET['status']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = $awalData + 1; foreach ($barang as $row) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <img src="assets/img/<?= esc($row['gambar']); ?>" width="50" height="50" class="object-fit-cover rounded">
                                    </td>
                                    <td><?= esc($row['nama_barang']); ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($row['nama_kategori'] ?? 'Tidak ada Kategori'); ?></span></td>
                                    <td><?= esc($row['stok']); ?></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-detail" 
                                            data-bs-toggle="modal" data-bs-target="#modalDetail"
                                            data-nama="<?= esc($row['nama_barang']); ?>"
                                            data-kategori="<?= esc($row['nama_kategori'] ?? 'Tidak ada Kategori'); ?>"
                                            data-stok="<?= esc($row['stok']); ?>"
                                            data-harga="<?= number_format($row['harga'], 0, ',', '.'); ?>"
                                            data-gambar="<?= esc($row['gambar']); ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <?php if (isAdmin()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit" 
                                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                data-id="<?= esc($row['id']); ?>"
                                                data-nama="<?= esc($row['nama_barang']); ?>"
                                                data-stok="<?= esc($row['stok']); ?>"
                                                data-harga="<?= esc($row['harga']); ?>"
                                                data-kategori="<?= esc($row['kategori_id']); ?>"
                                                data-gambar="<?= esc($row['gambar']); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <a href="#" class="btn btn-sm btn-outline-danger" onclick="konfirmasiHapus(<?= (int)$row['id']; ?>)">
                                               <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($jumlahHalaman > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-end">
                            
                            <?php 
                                $linkCari = ($keyword !== '') ? '&cari=' . urlencode($keyword) : ''; 
                            ?>

                            <?php if ($halamanAktif > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?halaman=<?= $halamanAktif - 1; ?><?= $linkCari; ?>">Previous</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                                <?php if ($i == $halamanAktif) : ?>
                                    <li class="page-item active"><span class="page-link"><?= $i; ?></span></li>
                                <?php else : ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?halaman=<?= $i; ?><?= $linkCari; ?>"><?= $i; ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($halamanAktif < $jumlahHalaman): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?halaman=<?= $halamanAktif + 1; ?><?= $linkCari; ?>">Next</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
                            <?php endif; ?>
                            
                        </ul>
                    </nav>
                    <?php endif; ?>

                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="bi bi-info-circle me-2"></i>Detail Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 text-center">
                        <img src="" id="detail-gambar" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="col-md-7">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%" class="text-muted">Nama Barang</th>
                                <td width="5%">:</td>
                                <td class="fw-bold" id="detail-nama"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Kategori</th>
                                <td>:</td>
                                <td><span class="badge bg-primary" id="detail-kategori"></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Stok Tersedia</th>
                                <td>:</td>
                                <td id="detail-stok"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Harga Satuan</th>
                                <td>:</td>
                                <td class="fw-bold text-success fs-5">Rp <span id="detail-harga"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_barang.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat) : ?>
                                <option value="<?= $kat['id']; ?>"><?= $kat['nama_kategori']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_barang.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="gambar_lama" id="edit-gambar-lama">

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" id="edit-kategori" class="form-select" required>
                            <?php foreach ($kategori as $kat) : ?>
                                <option value="<?= $kat['id']; ?>"><?= $kat['nama_kategori']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" id="edit-stok" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" id="edit-harga" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar (Biarkan kosong jika tidak diganti)</label>
                        <br>
                        <img src="" id="preview-gambar" width="80" class="mb-2 rounded border">
                        <input type="file" name="gambar" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Yakin ingin menghapus barang ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btn-hapus-confirm" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>

<script>
    // --- SCRIPT UNTUK MODAL DETAIL ---
    const detailBtns = document.querySelectorAll('.btn-detail');
    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('detail-nama').innerText = this.getAttribute('data-nama');
            document.getElementById('detail-kategori').innerText = this.getAttribute('data-kategori');
            document.getElementById('detail-stok').innerText = this.getAttribute('data-stok') + ' Unit';
            document.getElementById('detail-harga').innerText = this.getAttribute('data-harga');
            document.getElementById('detail-gambar').src = 'assets/img/' + this.getAttribute('data-gambar');
        });
    });

    // --- SCRIPT UNTUK MODAL EDIT ---
    const editBtns = document.querySelectorAll('.btn-edit');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.getAttribute('data-id');
            document.getElementById('edit-nama').value = this.getAttribute('data-nama');
            document.getElementById('edit-stok').value = this.getAttribute('data-stok');
            document.getElementById('edit-harga').value = this.getAttribute('data-harga');
            document.getElementById('edit-kategori').value = this.getAttribute('data-kategori');
            document.getElementById('edit-gambar-lama').value = this.getAttribute('data-gambar');
            document.getElementById('preview-gambar').src = 'assets/img/' + this.getAttribute('data-gambar');
        });
    });

    // --- SCRIPT UNTUK MODAL HAPUS ---
    function konfirmasiHapus(id) {
        var modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
        document.getElementById('btn-hapus-confirm').href = 'proses_barang.php?hapus=' + id;
        modalHapus.show();
    }
</script>
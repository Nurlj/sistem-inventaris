<?php
$page = 'kategori'; // Penanda untuk sidebar active
$title = 'Kategori';

require_once 'db/functions.php';
require_once 'components/header.php';

// fetch data kategori
$data_kategori = query("SELECT * FROM kategori ORDER BY id DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Kategori</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah Kategori
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        echo match($_GET['status']) {
                            'sukses_tambah' => "Kategori berhasil ditambahkan!",
                            'sukses_edit'   => "Kategori berhasil diperbarui!",
                            'sukses_hapus'  => "Kategori berhasil dihapus!",
                            default         => "Terjadi kesalahan."
                        };
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kategori</th>
                                    <?php if (isAdmin()): ?>
                                    <th width="15%">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach ($data_kategori as $row) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($row['nama_kategori']); ?></td>
                                    <?php if (isAdmin()): ?>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-id="<?= esc($row['id']); ?>"
                                                data-nama="<?= esc($row['nama_kategori']); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <a href="#" class="btn btn-sm btn-outline-danger" 
                                           onclick="konfirmasiHapus(<?= (int)$row['id']; ?>)">
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

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_kategori.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Elektronik" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_kategori.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit-nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
            <div class="modal-body">
                Yakin ingin menghapus kategori ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btn-hapus-confirm" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>

<script>
    // Script untuk mengisi Modal Edit secara Dinamis
    const editBtns = document.querySelectorAll('.btn-edit');
    const inputId = document.getElementById('edit-id');
    const inputNama = document.getElementById('edit-nama');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil data dari atribut tombol
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            // Masukkan ke dalam input modal
            inputId.value = id;
            inputNama.value = nama;
        });
    });

    // Script untuk Modal Hapus
    function konfirmasiHapus(id) {
        var modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
        var btnConfirm = document.getElementById('btn-hapus-confirm');
        
        // Set link href tombol hapus di modal
        btnConfirm.href = 'proses_kategori.php?hapus=' + id;
        
        modalHapus.show();
    }
</script>
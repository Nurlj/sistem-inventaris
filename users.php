<?php
$page = 'users';
$title = 'Management Users';

require_once 'db/functions.php';
require_once 'components/header.php';

// Cek Security: Hanya Admin yang boleh masuk sini
if (!isAdmin()) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!'); window.location='index.php';</script>";
    exit;
}

// fetch data user
$users = query("SELECT * FROM users ORDER BY id DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Management Users</h1>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-person-plus-fill"></i> Tambah User
                </button>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-<?php echo ($_GET['status'] === 'username_ada') ? 'danger' : 'success'; ?> alert-dismissible fade show">
                    <?php 
                        echo match($_GET['status']) {
                            'sukses_tambah' => "User baru berhasil ditambahkan.",
                            'sukses_edit'   => "Data user berhasil diupdate.",
                            'sukses_hapus'  => "User berhasil dihapus.",
                            'username_ada'  => "Gagal! Username sudah digunakan.",
                            default         => "Terjadi kesalahan."
                        };
                    ?>
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
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($users as $row) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($row['nama_lengkap']); ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($row['username']); ?></span></td>
                                    <td>
                                        <?php if ($row['role'] === 'admin'): ?>
                                            <span class="badge bg-primary">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark">Staff</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit" 
                                            data-bs-toggle="modal" data-bs-target="#modalEdit"
                                            data-id="<?= esc($row['id']); ?>"
                                            data-nama="<?= esc($row['nama_lengkap']); ?>"
                                            data-username="<?= esc($row['username']); ?>"
                                            data-role="<?= esc($row['role']); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <a href="#" class="btn btn-sm btn-outline-danger" onclick="konfirmasiHapus(<?= (int)$row['id']; ?>)">
                                           <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
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
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_users.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role / Hak Akses</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
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
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_users.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="(Biarkan kosong jika tidak diganti)">
                        <small class="text-muted fst-italic">*Kosongkan jika password tidak berubah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit-role" class="form-select">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
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
            <div class="modal-body">Yakin ingin menghapus user ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btn-hapus-confirm" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>

<script>
    const editBtns = document.querySelectorAll('.btn-edit');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.getAttribute('data-id');
            document.getElementById('edit-nama').value = this.getAttribute('data-nama');
            document.getElementById('edit-username').value = this.getAttribute('data-username');
            document.getElementById('edit-role').value = this.getAttribute('data-role');
        });
    });

    function konfirmasiHapus(id) {
        var modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
        document.getElementById('btn-hapus-confirm').href = 'proses_users.php?hapus=' + id;
        modalHapus.show();
    }
</script>
<?php 
// Set variable nama halaman
$page = 'dashboard';
$title = 'Dashboard';

require 'db/functions.php';
require_once 'components/header.php';



// 1. Hitung Total Jenis Barang
$q1 = mysqli_execute_query($connect, "SELECT COUNT(*) as total FROM barang");
$total_barang = $q1 ? (mysqli_fetch_assoc($q1)['total'] ?? 0) : 0;

// 2. Hitung Barang Masuk (HARI INI)
$tanggal_hari_ini = date('Y-m-d');
$q2 = mysqli_execute_query($connect, "SELECT SUM(jumlah) as total FROM barang_masuk WHERE tanggal = ?", [$tanggal_hari_ini]);
$masuk_hari_ini = $q2 ? (mysqli_fetch_assoc($q2)['total'] ?? 0) : 0;

// 3. Hitung Barang Keluar (HARI INI)
$q3 = mysqli_execute_query($connect, "SELECT SUM(jumlah) as total FROM barang_keluar WHERE tanggal = ?", [$tanggal_hari_ini]);
$keluar_hari_ini = $q3 ? (mysqli_fetch_assoc($q3)['total'] ?? 0) : 0;

// 4. Hitung Barang Stok Menipis (Misal: Stok < 5)
$batas_stok = 5;
$q4 = mysqli_execute_query($connect, "SELECT COUNT(*) as total FROM barang WHERE stok <= ?", [$batas_stok]);
$stok_menipis = $q4 ? (mysqli_fetch_assoc($q4)['total'] ?? 0) : 0;

// 5. Ambil Data Barang yang Stoknya Menipis (Untuk Tabel)
$barang_kritis = query("SELECT * FROM barang WHERE stok <= ? ORDER BY stok ASC LIMIT 5", [$batas_stok]);


?>

<div class="container-fluid">
    <div class="row">
        
        <?php require_once 'components/sidebar.php'; ?>

        <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2">Dashboard Overview</h1>
                    <p class="text-muted">Halo, <strong><?= esc($_SESSION['nama'] ?? 'User'); ?></strong>! Selamat datang kembali.</p>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="laporan.php" type="button" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Export Laporan
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Total Barang</h6>
                                    <h2 class="mb-0"><?= (int)$total_barang; ?></h2>
                                </div>
                                <i class="bi bi-box-seam fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Masuk (Hari Ini)</h6>
                                    <h2 class="mb-0"><?= (int)$masuk_hari_ini; ?></h2>
                                </div>
                                <i class="bi bi-arrow-down-square fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-danger text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Keluar (Hari Ini)</h6>
                                    <h2 class="mb-0"><?= (int)$keluar_hari_ini; ?></h2>
                                </div>
                                <i class="bi bi-arrow-up-square fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-warning text-dark h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Stok Menipis</h6>
                                    <h2 class="mb-0"><?= (int)$stok_menipis; ?></h2>
                                    <small class="text-danger fw-bold" style="font-size: 11px;">(Stok < 5)</small>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(count($barang_kritis) > 0) : ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-warning fw-bold"><i class="bi bi-exclamation-circle"></i> Peringatan Stok Menipis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Sisa Stok</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($barang_kritis as $row) : ?>
                                <tr>
                                    <td><?= esc($row['nama_barang']); ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?= esc($row['stok']); ?> Unit</span>
                                    </td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="data-barang.php" class="btn btn-sm btn-outline-primary">Lihat</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>
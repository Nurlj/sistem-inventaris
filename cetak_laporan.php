<?php
require_once 'db/functions.php';

// Ambil parameter dari URL (GET)
$jenis = $_GET['jenis'] ?? '';
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

// Validasi Akses Langsung
if (empty($jenis)) {
    die("Silakan pilih laporan terlebih dahulu.");
}

$judul = "";
$data = [];

// Logic Query (Prepared statement untuk mencegah SQL injection melalui GET parameter)
if ($jenis === 'stok') {
    $judul = "Laporan Stok Barang";
    $data = query("SELECT barang.*, kategori.nama_kategori FROM barang LEFT JOIN kategori ON barang.kategori_id = kategori.id");
} elseif ($jenis === 'masuk' && $tgl_mulai !== '' && $tgl_selesai !== '') {
    $judul = "Laporan Barang Masuk (" . esc($tgl_mulai) . " s/d " . esc($tgl_selesai) . ")";
    $data = query("SELECT bm.*, b.nama_barang FROM barang_masuk bm JOIN barang b ON bm.barang_id = b.id WHERE bm.tanggal BETWEEN ? AND ?", [$tgl_mulai, $tgl_selesai]);
} elseif ($jenis === 'keluar' && $tgl_mulai !== '' && $tgl_selesai !== '') {
    $judul = "Laporan Barang Keluar (" . esc($tgl_mulai) . " s/d " . esc($tgl_selesai) . ")";
    $data = query("SELECT bk.*, b.nama_barang FROM barang_keluar bk JOIN barang b ON bk.barang_id = b.id WHERE bk.tanggal BETWEEN ? AND ?", [$tgl_mulai, $tgl_selesai]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Khusus Print */
        body { font-family: 'Times New Roman', serif; }
        .kop-surat { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .tanda-tangan { margin-top: 50px; text-align: right; }
        @media print {
            .no-print { display: none; } /* Hilangkan tombol kembali saat print */
        }
    </style>
</head>
<body onload="window.print()"> <div class="container mt-4">
        
        <div class="text-center kop-surat">
            <h2 class="fw-bold">GUDANG KITA INDONESIA</h2>
            <p>Jl. Jakarta Blok BA No. 01 Loa Bakung<br>
            Telp: (0812) 555-666 | Email: test@gmail.com</p>
        </div>

        <div class="text-center mb-4">
            <h4 class="text-uppercase text-decoration-underline"><?= esc($judul); ?></h4>
            <?php if ($jenis !== 'stok' && $tgl_mulai !== '' && $tgl_selesai !== ''): ?>
                <small>Periode: <?= esc(date('d F Y', strtotime($tgl_mulai))); ?> - <?= esc(date('d F Y', strtotime($tgl_selesai))); ?></small>
            <?php endif; ?>
        </div>

        <table class="table table-bordered border-dark">
            <thead>
                <tr class="table-secondary border-dark">
                    <th width="5%">No</th>
                    <?php if ($jenis === 'stok'): ?>
                         <th>Nama Barang</th>
                         <th>Kategori</th>
                         <th>Stok</th>
                         <th>Harga Satuan</th>
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
                    <td class="text-center"><?= $no++; ?></td>
                    <?php if ($jenis === 'stok'): ?>
                        <td><?= esc($row['nama_barang']); ?></td>
                        <td><?= esc($row['nama_kategori'] ?? 'Tidak ada Kategori'); ?></td>
                        <td class="text-center"><?= esc($row['stok']); ?></td>
                        <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
                    <?php else: ?>
                        <td><?= esc(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                        <td><?= esc($row['nama_barang']); ?></td>
                        <td class="text-center"><?= esc($row['jumlah']); ?></td>
                        <td><?= esc($row['keterangan'] ?? ''); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tanda-tangan">
            <p>Samarinda, <?= esc(date('d F Y')); ?></p>
            <br><br><br>
            <p><strong>( Admin Gudang )</strong></p>
        </div>
        
        <div class="no-print mt-4">
            <button onclick="window.history.back()" class="btn btn-secondary">Kembali</button>
        </div>

    </div>

</body>
</html>
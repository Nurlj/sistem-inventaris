<?php
require_once 'db/functions.php';

// 1. PROSES BARANG MASUK
if (isset($_POST['tambah_masuk'])) {
    $barang_id  = (int)($_POST['barang_id'] ?? 0);
    $tanggal    = $_POST['tanggal'] ?? '';
    $jumlah     = (int)($_POST['jumlah'] ?? 0);
    $keterangan = $_POST['keterangan'] ?? '';

    // 1. Simpan ke tabel riwayat (barang_masuk) menggunakan prepared statement
    $query1 = "INSERT INTO barang_masuk (barang_id, tanggal, jumlah, keterangan) VALUES (?, ?, ?, ?)";
    
    // 2. Update stok di tabel barang (Stok Bertambah)
    $query2 = "UPDATE barang SET stok = stok + ? WHERE id = ?";

    // Jalankan kedua query dengan prepared statements
    if (mysqli_execute_query($connect, $query1, [$barang_id, $tanggal, $jumlah, $keterangan]) && 
        mysqli_execute_query($connect, $query2, [$jumlah, $barang_id])) {
        header("Location: barang-masuk.php?status=sukses");
    } else {
        header("Location: barang-masuk.php?status=gagal");
    }
    exit;
}

// 2. PROSES BARANG KELUAR
if (isset($_POST['tambah_keluar'])) {
    $barang_id  = (int)($_POST['barang_id'] ?? 0);
    $tanggal    = $_POST['tanggal'] ?? '';
    $jumlah     = (int)($_POST['jumlah'] ?? 0);
    $keterangan = $_POST['keterangan'] ?? '';

    // Cek Stok Dulu (Cukup gak?) menggunakan prepared statement
    $cekStok = mysqli_execute_query($connect, "SELECT stok FROM barang WHERE id = ?", [$barang_id]);
    
    if ($cekStok && mysqli_num_rows($cekStok) > 0) {
        $data = mysqli_fetch_assoc($cekStok);
        if ($data['stok'] < $jumlah) {
            // Jika stok kurang, tendang balik
            header("Location: barang-keluar.php?status=stok_kurang");
            exit;
        }
    } else {
        header("Location: barang-keluar.php?status=gagal");
        exit;
    }

    // 1. Simpan ke tabel riwayat (barang_keluar)
    $query1 = "INSERT INTO barang_keluar (barang_id, tanggal, jumlah, keterangan) VALUES (?, ?, ?, ?)";
    
    // 2. Update stok di tabel barang (Stok Berkurang)
    $query2 = "UPDATE barang SET stok = stok - ? WHERE id = ?";

    if (mysqli_execute_query($connect, $query1, [$barang_id, $tanggal, $jumlah, $keterangan]) && 
        mysqli_execute_query($connect, $query2, [$jumlah, $barang_id])) {
        header("Location: barang-keluar.php?status=sukses");
    } else {
        header("Location: barang-keluar.php?status=gagal");
    }
    exit;
}

// 3. PROSES HAPUS RIWAYAT (Stok dikembalikan)
if (isset($_GET['hapus_masuk'])) {

    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak!'); window.location='barang-masuk.php';</script>";
        exit;
    }

    $id = (int)$_GET['hapus_masuk'];
    
    // Ambil data dulu sebelum dihapus untuk tahu jumlahnya
    $cek = mysqli_execute_query($connect, "SELECT * FROM barang_masuk WHERE id = ?", [$id]);
    
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $jumlah = (int)$row['jumlah'];
        $barang_id = (int)$row['barang_id'];

        // Hapus data
        mysqli_execute_query($connect, "DELETE FROM barang_masuk WHERE id = ?", [$id]);
        // Kurangi stok barang (Reverse)
        mysqli_execute_query($connect, "UPDATE barang SET stok = stok - ? WHERE id = ?", [$jumlah, $barang_id]);

        header("Location: barang-masuk.php?status=sukses_hapus");
    } else {
        header("Location: barang-masuk.php?status=gagal");
    }
    exit;
}

// Hapus Barang Keluar (Stok barang harus ditambah lagi)
if (isset($_GET['hapus_keluar'])) {

    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak!'); window.location='barang-keluar.php';</script>";
        exit;
    }

    $id = (int)$_GET['hapus_keluar'];
    
    $cek = mysqli_execute_query($connect, "SELECT * FROM barang_keluar WHERE id = ?", [$id]);
    
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $jumlah = (int)$row['jumlah'];
        $barang_id = (int)$row['barang_id'];

        mysqli_execute_query($connect, "DELETE FROM barang_keluar WHERE id = ?", [$id]);
        // Tambah stok barang (Reverse)
        mysqli_execute_query($connect, "UPDATE barang SET stok = stok + ? WHERE id = ?", [$jumlah, $barang_id]);

        header("Location: barang-keluar.php?status=sukses_hapus");
    } else {
        header("Location: barang-keluar.php?status=gagal");
    }
    exit;
}
?>
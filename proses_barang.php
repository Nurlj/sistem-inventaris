<?php

require_once 'db/functions.php';

// FUNGSI UPLOAD GAMBAR
function upload_gambar(): string|bool {
    $namaFile   = $_FILES['gambar']['name'] ?? '';
    $ukuranFile = $_FILES['gambar']['size'] ?? 0;
    $error      = $_FILES['gambar']['error'] ?? 4;
    $tmpName    = $_FILES['gambar']['tmp_name'] ?? '';

    // 1. Cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        return 'default.png'; 
    }

    // 2. Cek ekstensi gambar valid menggunakan pathinfo
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid, true)) {
        echo "<script>
                alert('File yang diupload bukan gambar!');
                window.location.href='data-barang.php';
              </script>";
        return false;
    }

    // 3. Cek ukuran (Max 2MB)
    if ($ukuranFile > 2000000) {
        echo "<script>
                alert('Ukuran gambar terlalu besar! Max 2MB');
                window.location.href='data-barang.php';
              </script>";
        return false;
    }

    // 4. Generate nama file baru
    $namaFileBaru = uniqid('', true) . '.' . $ekstensiGambar;

    // Pindahkan file ke folder tujuan
    move_uploaded_file($tmpName, 'assets/img/' . $namaFileBaru);

    return $namaFileBaru;
}

// PROSES TAMBAH DATA
if (isset($_POST['simpan'])) {
    // Menyimpan data mentah, perlindungan SQL injection diatasi oleh prepared statement.
    // XSS diatasi saat rendering output.
    $nama     = $_POST['nama_barang'] ?? '';
    $kategori = $_POST['kategori_id'] ?? null;
    $stok     = (int)($_POST['stok'] ?? 0);
    $harga    = (int)($_POST['harga'] ?? 0);

    // Proses Upload
    $gambar = upload_gambar();
    
    // Jika upload gagal (return false), hentikan proses
    if (!$gambar) {
        return false; 
    }

    // Menggunakan mysqli_execute_query untuk mengamankan data
    $query = "INSERT INTO barang (nama_barang, gambar, stok, harga, kategori_id) VALUES (?, ?, ?, ?, ?)";
    $success = mysqli_execute_query($connect, $query, [$nama, $gambar, $stok, $harga, $kategori]);

    if ($success) {
        header("Location: data-barang.php?status=sukses_tambah");
    } else {
        header("Location: data-barang.php?status=gagal");
    }
    exit;
}

// PROSES UPDATE DATA
if (isset($_POST['update'])) {

    // Cek Akses Admin
    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh edit.'); window.location='data-barang.php';</script>";
        exit;
    }

    $id          = (int)($_POST['id'] ?? 0);
    $nama        = $_POST['nama_barang'] ?? '';
    $kategori    = $_POST['kategori_id'] ?? null;
    $stok        = (int)($_POST['stok'] ?? 0);
    $harga       = (int)($_POST['harga'] ?? 0);
    $gambarLama  = $_POST['gambar_lama'] ?? 'default.png';

    // Cek apakah user pilih gambar baru atau tidak
    if (($_FILES['gambar']['error'] ?? 4) === 4) {
        $gambar = $gambarLama; // Pakai gambar lama
    } else {
        $gambar = upload_gambar(); // Upload gambar baru
        
        // Jangan hapus jika gambar lama adalah default.png / default.jpg
        if ($gambar && $gambarLama !== 'default.png' && $gambarLama !== 'default.jpg' && file_exists("assets/img/$gambarLama")) {
            unlink("assets/img/$gambarLama");
        }
    }

    if ($gambar === false) {
        return false;
    }

    // Menggunakan mysqli_execute_query untuk mengamankan proses update
    $query = "UPDATE barang SET 
                nama_barang = ?,
                kategori_id = ?,
                stok        = ?,
                harga       = ?,
                gambar      = ?
              WHERE id = ?";
    $success = mysqli_execute_query($connect, $query, [$nama, $kategori, $stok, $harga, $gambar, $id]);

    if ($success) {
        header("Location: data-barang.php?status=sukses_edit");
    } else {
        header("Location: data-barang.php?status=gagal");
    }
    exit;
}

// PROSES HAPUS DATA
if (isset($_GET['hapus'])) {

    // Cek Akses Admin
    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh edit.'); window.location='data-barang.php';</script>";
        exit;
    }

    $id = (int)$_GET['hapus'];

    // Ambil data gambar dulu untuk dihapus dari folder menggunakan prepared statement
    $result = mysqli_execute_query($connect, "SELECT gambar FROM barang WHERE id = ?", [$id]);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gambar = $row['gambar'] ?? 'default.png';

        // Jangan hapus file fisik jika itu adalah default.png / default.jpg
        if ($gambar !== 'default.png' && $gambar !== 'default.jpg' && file_exists("assets/img/$gambar")) {
            unlink("assets/img/$gambar");
        }
    }

    // Hapus data dari database menggunakan prepared statement
    $query = "DELETE FROM barang WHERE id = ?";
    $success = mysqli_execute_query($connect, $query, [$id]);

    if ($success) {
        header("Location: data-barang.php?status=sukses_hapus");
    } else {
        header("Location: data-barang.php?status=gagal");
    }
    exit;
}
?>
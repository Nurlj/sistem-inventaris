<?php 

require_once 'db/functions.php';

// --- PROSES TAMBAH ---
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_kategori'] ?? '';
    
    $query = "INSERT INTO kategori (nama_kategori) VALUES (?)";
    $success = mysqli_execute_query($connect, $query, [$nama]);
    
    if ($success) {
        header("Location: kategori.php?status=sukses_tambah");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
}

// --- PROSES EDIT ---
if (isset($_POST['update'])) {

    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh edit.'); window.location='kategori.php';</script>";
        exit;
    }

    $id   = (int)($_POST['id'] ?? 0);
    $nama = $_POST['nama_kategori'] ?? '';

    $query = "UPDATE kategori SET nama_kategori = ? WHERE id = ?";
    $success = mysqli_execute_query($connect, $query, [$nama, $id]);

    if ($success) {
        header("Location: kategori.php?status=sukses_edit");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
}

// --- PROSES HAPUS ---
if (isset($_GET['hapus'])) {

    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh edit.'); window.location='kategori.php';</script>";
        exit;
    }

    $id = (int)$_GET['hapus'];

    $query = "DELETE FROM kategori WHERE id = ?";
    $success = mysqli_execute_query($connect, $query, [$id]);

    if ($success) {
        header("Location: kategori.php?status=sukses_hapus");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
}
?>
<?php
require_once 'db/functions.php';

// 1. TAMBAH USER
if (isset($_POST['simpan'])) {
    
    // CEK KEAMANAN: Hanya Admin yang boleh tambah user
    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak!'); window.location='users.php';</script>";
        exit;
    }

    $nama     = $_POST['nama_lengkap'] ?? '';
    $username = strtolower(trim($_POST['username'] ?? ''));
    $pass     = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    // Cek Username Duplikat menggunakan prepared statements (mysqli_execute_query)
    $cek = mysqli_execute_query($connect, "SELECT username FROM users WHERE username = ?", [$username]);
    if ($cek && mysqli_num_rows($cek) > 0) {
        header("Location: users.php?status=username_ada");
        exit;
    }

    // Enkripsi Password
    $password_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Query INSERT dengan prepared statement
    $query = "INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)";
    $success = mysqli_execute_query($connect, $query, [$nama, $username, $password_hash, $role]);

    if ($success) {
        header("Location: users.php?status=sukses_tambah");
    } else {
        header("Location: users.php?status=gagal");
    }
    exit;
}

// 2. EDIT USER
if (isset($_POST['update'])) {

    // CEK KEAMANAN
    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak!'); window.location='users.php';</script>";
        exit;
    }

    $id       = (int)($_POST['id'] ?? 0);
    $nama     = $_POST['nama_lengkap'] ?? '';
    $username = strtolower(trim($_POST['username'] ?? ''));
    $pass     = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    // Logika Password:
    // Jika kolom password diisi, maka update password baru.
    if ($pass !== '') {
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        // Update Query: Sertakan role dan password baru
        $query = "UPDATE users SET 
                    nama_lengkap = ?, 
                    username     = ?, 
                    password     = ?, 
                    role         = ? 
                  WHERE id = ?";
        $success = mysqli_execute_query($connect, $query, [$nama, $username, $password_hash, $role, $id]);
    } else {
        // Jika kosong, biarkan password lama, tapi tetap update role
        $query = "UPDATE users SET 
                    nama_lengkap = ?, 
                    username     = ?, 
                    role         = ? 
                  WHERE id = ?";
        $success = mysqli_execute_query($connect, $query, [$nama, $username, $role, $id]);
    }

    if ($success) {
        header("Location: users.php?status=sukses_edit");
    } else {
        header("Location: users.php?status=gagal");
    }
    exit;
}

// 3. HAPUS USER
if (isset($_GET['hapus'])) {

    // CEK KEAMANAN
    if (!isAdmin()) {
        echo "<script>alert('Akses Ditolak!'); window.location='users.php';</script>";
        exit;
    }

    $id = (int)$_GET['hapus'];
    
    // CEK TAMBAHAN: Jangan biarkan admin menghapus dirinya sendiri saat sedang login
    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        echo "<script>alert('Anda tidak dapat menghapus akun yang sedang digunakan!'); window.location='users.php';</script>";
        exit;
    }

    $query = "DELETE FROM users WHERE id = ?";
    $success = mysqli_execute_query($connect, $query, [$id]);

    if ($success) {
        header("Location: users.php?status=sukses_hapus");
    } else {
        header("Location: users.php?status=gagal");
    }
    exit;
}
?>
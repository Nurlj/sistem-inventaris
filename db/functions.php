<?php
// Memulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$host = "localhost";
$user = "root"; // sesuaikan dengan username phpmyadmin punya kamu
$pass = "12345"; // kalo gk ada, passwordnya nya dikosongin aja
$db = "sistem-inventaris"; // sesuaikan juga nama db buatan kamu

$connect = mysqli_connect($host, $user, $pass, $db);

// Jika koneksi gagal nampilin pesan error
if (!$connect) {
    die("Koneksi gagal : " . mysqli_connect_error());
}

/**
 * Fungsi helper untuk mencegah Cross-Site Scripting (XSS) pada output HTML.
 * Menggunakan fitur deklarasi tipe PHP 8.
 */
function esc(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fungsi fetch data dengan prepared statements (prosedural modern).
 * Menggunakan mysqli_execute_query (tersedia di PHP 8.2+ / 8.4).
 */
function query(string $query, array $params = []): array
{
    global $connect;
    $result = mysqli_execute_query($connect, $query, $params);

    if ($result === false) {
        return [];
    }

    // Jika query bukan SELECT (seperti INSERT/UPDATE/DELETE), return true/false
    if ($result === true) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Fungsi registrasi user baru dengan prepared statements.
 */
function registrasi(array $data): int|bool
{
    global $connect;

    // Simpan data mentah, XSS protection dilakukan saat output (escaping)
    $nama_lengkap = $data['nama_lengkap'] ?? '';
    $username     = strtolower(trim($data['username'] ?? ''));
    $password     = $data['password'] ?? '';
    $password2    = $data['password2'] ?? '';

    // Set Default Role untuk pendaftar baru
    $role = 'staff';

    // Cek Username dengan prepared statements (mysqli_execute_query)
    $result = mysqli_execute_query($connect, "SELECT username FROM users WHERE username = ?", [$username]);
    if (mysqli_fetch_assoc($result)) {
        echo "<script>alert('Username yang Anda masukkan sudah terdaftar!')</script>";
        return false;
    }

    // Cek Konfirmasi Password
    if ($password !== $password2) {
        echo "<script>alert('Konfirmasi password tidak sama!')</script>";
        return false;
    }

    // Enkripsi Password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert Data menggunakan mysqli_execute_query
    $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)";
    $success = mysqli_execute_query($connect, $query, [$username, $password_hash, $nama_lengkap, $role]);

    if ($success) {
        return mysqli_affected_rows($connect);
    }
    return false;
}

/**
 * Fungsi untuk cek Role user.
 */
function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Fungsi alert dan redirect.
 */
function redirect(string $text, string $link): void
{
    // Escaping text untuk mencegah XSS pada eksekusi JS alert
    $safe_text = esc($text);
    $safe_link = esc($link);
    echo "<script>
        alert('$safe_text');
        document.location.href = '$safe_link';
        </script>";
    exit;
}

/**
 * Cek Login Global
 * Otomatis redirect ke login.php jika belum login,
 * kecuali jika sedang berada di halaman public (login.php / register.php).
 */
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'register.php'];

if (!in_array($current_page, $public_pages)) {
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }
}

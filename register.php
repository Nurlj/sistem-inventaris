<?php 
require 'db/functions.php';

if (isset($_POST['register'])) {
    if (registrasi($_POST) > 0) {
        redirect("user baru berhasil ditambahkan!", "register.php");
    } else {
        // Jangan cetak mysqli_error ke layar di production demi keamanan, beri pesan umum atau aman.
        echo "<script>alert('Gagal menambahkan user baru!');</script>";
    }
} 

?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Akun - Sistem Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
      body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .card-register {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden; /* Agar border radius gambar tetap rapi */
      }
      .btn-primary {
        background-color: #0d6efd;
        border: none;
        padding: 10px;
        font-weight: 600;
      }
      .btn-primary:hover {
        background-color: #0b5ed7;
      }
      .form-control:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        border-color: #0d6efd;
      }
    </style>
  </head>
  <body class="d-flex align-items-center min-vh-100 py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card card-register">
            <div class="card-body p-4 p-md-5">
              <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px">
                  <i class="bi bi-person-plus-fill fs-2"></i>
                </div>
                <h4 class="fw-bold">Buat Akun Baru</h4>
                <p class="text-muted small">Gabung sekarang untuk mengelola stok gudang Anda.</p>
              </div>

              <form action="" method="post">
                <div class="mb-3">
                  <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="nama_lengkap" class="form-control border-start-0 ps-0" id="nama_lengkap" required />
                  </div>
                </div>

                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" id="username" required />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="password2" class="form-label">Ulangi Password</label>
                    <input type="password" name="password2" class="form-control" id="password2" required />
                  </div>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" name="register" class="btn btn-primary">Daftar Sekarang</button>
                </div>
              </form>

              <hr class="my-4" />

              <div class="text-center">
                <span class="text-muted small">Sudah punya akun?</span>
                <a href="login.php" class="fw-bold text-decoration-none ms-1">Masuk Disini</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

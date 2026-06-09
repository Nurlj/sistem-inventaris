<?php 

require 'db/functions.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    // Membaca data mentah dari POST, SQL Injection ditangani oleh prepared statements.
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Menggunakan mysqli_execute_query untuk mengamankan query SELECT dari SQL Injection
    $result = mysqli_execute_query($connect, "SELECT * FROM users WHERE username = ?", [$username]);

    // Cek username
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek password
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama']    = $row['nama_lengkap'];
            $_SESSION['role']    = $row['role'];            

            // Redirect ke dashboard
            header("Location: index.php");
            exit;
        }
    }

    // Jika salah username/password
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Sistem Inventaris</title>
    <link rel="icon" href="assets/icon/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
      body {
        background-color: #f0f2f5;
      }
      .card-login {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      }
      .btn-primary {
        background-color: #4e73df;
        border: none;
      }
    </style>
  </head>
  <body class="d-flex align-items-center min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
          <!-- alert -->
          <?php if(isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Username atau Password Salah!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <!-- end alert -->
          <div class="card card-login p-4">
            <div class="card-body">
              <div class="text-center mb-4">
                <i class="bi bi-box-seam-fill text-primary" style="font-size: 3rem"></i>
                <h4 class="mt-2">Inventory System</h4>
                <p class="text-muted">Masuk untuk mengelola stok</p>
              </div>

              <form action="" method="post">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="username" name="username" class="form-control" id="username"/>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" id="password"/>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" name="login" class="btn btn-primary py-2">Login</button>
                </div>
              </form>

              <div class="mt-4 text-center">
                <small>Belum punya akun? <a href="register.php" class="text-decoration-none">Register disini</a></small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

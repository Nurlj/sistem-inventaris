<div class="col-lg-2 d-none d-lg-block sidebar p-3">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none px-3">
        <i class="bi bi-box-seam-fill fs-4 me-2 text-primary"></i>
        <span class="fs-4 fw-bold text-dark">GudangKita</span>
    </a>
    
    <hr />
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>"> 
                <i class="bi bi-speedometer2 me-2"></i> Dashboard 
            </a>
        </li>
        <li>
            <a href="data-barang.php" class="nav-link <?php echo ($page == 'barang') ? 'active' : ''; ?>"> 
                <i class="bi bi-box me-2"></i> Data Barang 
            </a>
        </li>
        <li>
            <a href="kategori.php" class="nav-link <?php echo ($page == 'kategori') ? 'active' : ''; ?>"> <i class="bi bi-tags me-2"></i> Kategori </a>
        </li>
        
        <div class="sidebar-heading">Transaksi</div>
        
        <li>
            <a href="barang-masuk.php" class="nav-link <?php echo ($page == 'barang-masuk') ? 'active' : ''; ?>"> <i class="bi bi-arrow-down-circle me-2"></i> Barang Masuk </a>
        </li>
        <li>
            <a href="barang-keluar.php" class="nav-link <?php echo ($page == 'barang-keluar') ? 'active' : ''; ?>"> <i class="bi bi-arrow-up-circle me-2"></i> Barang Keluar </a>
        </li>
        
        <div class="sidebar-heading">Admin</div>
        
        <li>
            <a href="laporan.php" class="nav-link <?php echo ($page == 'laporan') ? 'active' : ''; ?>"> <i class="bi bi-file-earmark-text me-2"></i> Laporan </a>
        </li>
        <?php if(isAdmin()): ?>
        <li>
            <a href="users.php" class="nav-link <?php echo ($page == 'users') ? 'active' : ''; ?>"> <i class="bi bi-people me-2"></i> Users </a>
        </li>
        <?php endif; ?>
    </ul>
    
    <hr />
    
    <div class="px-3 pb-2">
        <div class="d-flex align-items-center mb-3">
            <div class="d-flex flex-column">
                <strong class="text-dark"><?= $_SESSION['nama']; ?></strong>
                <small class="text-muted" style="font-size: 12px;"><?= $_SESSION['role']; ?></small>
            </div>
        </div>
        
        <button type="button" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center btn-sm" data-bs-toggle="modal" data-bs-target="#modalLogout">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </button>
    </div>
</div>

<div class="offcanvas offcanvas-start bg-white text-dark" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-primary">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="index.php" class="nav-link <?php echo ($page == 'dashboard') ? 'active' : 'text-dark'; ?>">Dashboard</a></li>
            <li><a href="data-barang.php" class="nav-link <?php echo ($page == 'barang') ? 'active' : 'text-dark'; ?>">Data Barang</a></li>
            <li><a href="barang-masuk.php" class="nav-link <?php echo ($page == 'barang-masuk') ? 'active' : 'text-dark'; ?>">Barang Masuk</a></li>
            <li><a href="barang-keluar.php" class="nav-link <?php echo ($page == 'barang-keluar') ? 'active' : 'text-dark'; ?>">Barang Keluar</a></li>
            <li><hr /></li>
            <li>
                <a href="logout.php" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#modalLogout">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalLogoutLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin keluar dari sistem?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="logout.php" class="btn btn-danger">Ya, Logout</a>
      </div>
    </div>
  </div>
</div>
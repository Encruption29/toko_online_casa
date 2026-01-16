<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="../toko_online">Toko Online</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item me-4">
          <a class="nav-link" href="../toko_online">Beranda</a>
        </li>
        <li class="nav-item me-4">
          <a class="nav-link" href="produk.php">Produk</a>
        </li>
        <li class="nav-item me-4">
          <a class="nav-link" href="tentang_kami.php">Tentang Kami</a>
        </li>
      </ul>
      
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-center">
        <li class="nav-item me-3">
          <a class="nav-link position-relative" href="keranjang.php">
            <i class="fas fa-shopping-cart fa-lg text-white"></i>
            <?php if(isset($_SESSION['login_pembeli'])): 
              // Opsional: Menghitung jumlah item di keranjang
              $uid = $_SESSION['user_id'];
              $queryCount = mysqli_query($conn, "SELECT id FROM keranjang WHERE user_id='$uid'");
              $count = mysqli_num_rows($queryCount);
              if($count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                  <?php echo $count; ?>
                </span>
              <?php endif; 
            endif; ?>
          </a>
        </li>

        <?php if(isset($_SESSION['login_pembeli'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle btn btn-dark px-3 text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item text-dark" href="profil.php"><i class="fas fa-user-edit me-2"></i> Edit Profil</a></li>
              <li><a class="dropdown-item text-dark" href="history.php"><i class="fas fa-history me-2"></i> Riwayat Belanja</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link btn btn-dark text-white px-4" href="login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
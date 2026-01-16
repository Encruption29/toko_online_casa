<?php
    session_start(); // Wajib di baris paling atas untuk mengecek status login
    require "koneksi.php";

    // Ambil nama produk dari URL dan bersihkan untuk keamanan
    $nama = htmlspecialchars($_GET['nama']);
    
    // Query untuk mendapatkan detail produk berdasarkan nama
    $queryProduk = mysqli_query($conn, "SELECT * FROM produk WHERE nama='$nama'");
    $produk = mysqli_fetch_array($queryProduk);
    if(!$produk){
        header("location: produk.php");
        exit();
    }

    // Query untuk mendapatkan produk terkait
    $queryProdukTerkait = mysqli_query($conn, "SELECT * FROM produk WHERE kategori_id='$produk[kategori_id]' AND id!='$produk[id]' LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Detail Produk</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require "navbar.php"; ?>

    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-3">
                    <img src="image/<?php echo $produk['foto']; ?>" class="w-100 img-thumbnail" alt="">
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <h1><?php echo $produk['nama']; ?></h1>
                    <p class="fs-5">
                        <?php echo $produk['detail']; ?>
                    </p>
                    <p class="text-harga">
                        Rp <?php echo number_format($produk['harga']); ?>
                    </p>
                    <p class="fs-5">
                        Status: 
                        <strong>
                            <?php 
                                if($produk['ketersediaan_stok'] > 0) {
                                    echo "Tersedia (" . $produk['ketersediaan_stok'] . " unit)"; 
                                } else {
                                    echo "<span class='text-danger'>Habis</span>";
                                }
                            ?>
                        </strong>
                    </p>

                    <div class="mt-4">
                        <form action="keranjang_tambah.php" method="post">
                            <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Beli</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" style="width: 100px;" value="1" min="1" max="<?php echo $produk['ketersediaan_stok']; ?>" required>
                            </div>

                            <?php if(!isset($_SESSION['login_pembeli'])): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i> Silahkan <a href="login.php" class="alert-link">Login</a> untuk belanja.
                                </div>
                            <?php elseif($produk['ketersediaan_stok'] <= 0): ?>
                                <button type="button" class="btn btn-secondary w-100" disabled>Stok Habis</button>
                            <?php else: ?>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="add_to_cart" class="btn btn-outline-primary">
                                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5 warna2">
        <div class="container">
            <h2 class="text-center text-white mb-5">Produk Terkait</h2>
            <div class="row">
                <?php while($data = mysqli_fetch_array($queryProdukTerkait)){ ?>
                <div class="col-md-6 col-lg-3 mb-3">
                    <a href="produk_detail.php?nama=<?php echo $data['nama']; ?>">
                        <img src="image/<?php echo $data['foto']; ?>" class="img-fluid img-thumbnail produk-terkait-image" alt="">
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php require "footer.php"; ?>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
</body>
</html>
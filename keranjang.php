<?php
session_start();
require "koneksi.php";

// Proteksi halaman: Hanya user yang sudah login bisa akses
if (!isset($_SESSION['login_pembeli'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Logika Hapus Item
if (isset($_GET['hapus'])) {
    $id_keranjang = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id = '$id_keranjang' AND user_id = '$user_id'");
    header("location: keranjang.php");
}

// Ambil data keranjang beserta detail produknya
$query = mysqli_query($conn, "SELECT k.id as id_keranjang, k.jumlah, p.nama, p.harga, p.foto, p.id as id_produk 
                              FROM keranjang k 
                              JOIN produk p ON k.produk_id = p.id 
                              WHERE k.user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <?php require "navbar.php"; ?>

    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-shopping-cart text-primary"></i> Keranjang Belanja Anda</h2>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_belanja = 0;
                                if (mysqli_num_rows($query) > 0) {
                                    while ($item = mysqli_fetch_array($query)) { 
                                        $subtotal = $item['harga'] * $item['jumlah'];
                                        $total_belanja += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="image/<?= $item['foto']; ?>" class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <span class="fw-bold"><?= $item['nama']; ?></span>
                                        </div>
                                    </td>
                                    <td>Rp <?= number_format($item['harga']); ?></td>
                                    <td><?= $item['jumlah']; ?></td>
                                    <td class="fw-bold">Rp <?= number_format($subtotal); ?></td>
                                    <td>
                                        <a href="keranjang.php?hapus=<?= $item['id_keranjang']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus item ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-cart-arrow-down fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Keranjang Anda masih kosong.</p>
                                        <a href="produk.php" class="btn btn-primary">Mulai Belanja</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Ringkasan Belanja</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Harga</span>
                            <span class="fw-bold text-primary fs-5">Rp <?= number_format($total_belanja); ?></span>
                        </div>
                        
                        <?php if ($total_belanja > 0): ?>
                            <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                                Lanjut ke Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 btn-lg" disabled>Checkout</button>
                        <?php endif; ?>
                        
                        <a href="produk.php" class="btn btn-link w-100 mt-2 text-decoration-none">
                            <i class="fas fa-chevron-left"></i> Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require "footer.php"; ?>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
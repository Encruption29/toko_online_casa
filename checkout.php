<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['login_pembeli'])) { header("location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
// Ambil data user untuk isi otomatis alamat
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_array($queryUser);

// Ambil item dari keranjang
$queryKeranjang = mysqli_query($conn, "SELECT k.*, p.nama, p.harga, p.foto FROM keranjang k 
                                      JOIN produk p ON k.produk_id = p.id 
                                      WHERE k.user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Toko Online</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container py-5">
        <h2 class="mb-4">Selesaikan Pemesanan</h2>
        <form action="proses_checkout.php" method="post">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card p-4 shadow-sm mb-4">
                        <h5><i class="fas fa-map-marker-alt text-danger"></i> Alamat Pengiriman</h5>
                        <hr>
                        <div class="mb-3">
                            <label>Nama Penerima</label>
                            <input type="text" name="nama_penerima" class="form-control" value="<?= $user['nama'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" required><?= $user['alamat'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <select name="metode" class="form-select" required>
                                <option value="Transfer Bank">Transfer Bank (BCA/Mandiri)</option>
                                <option value="COD">Bayar di Tempat (COD)</option>
                                <option value="E-Wallet">Gopay / OVO / Dana</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card p-4 shadow-sm border-primary">
                        <h5>Ringkasan Belanja</h5>
                        <hr>
                        <?php 
                        $total_bayar = 0;
                        while($item = mysqli_fetch_array($queryKeranjang)): 
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $total_bayar += $subtotal;
                        ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= $item['nama'] ?> (x<?= $item['jumlah'] ?>)</span>
                            <span>Rp <?= number_format($subtotal) ?></span>
                        </div>
                        <?php endwhile; ?>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span class="text-primary">Rp <?= number_format($total_bayar) ?></span>
                        </div>
                        <input type="hidden" name="total_harga" value="<?= $total_bayar ?>">
                        <button type="submit" name="checkout" class="btn btn-primary w-100 btn-lg mt-4">Konfirmasi & Pesan Sekarang</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php require "footer.php"; ?>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
</body>
</html>
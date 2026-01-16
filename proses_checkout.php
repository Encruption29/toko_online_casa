<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['login_pembeli'])) {
    header("location: login.php");
    exit;
}

if(isset($_POST['checkout'])){
    $user_id = $_SESSION['user_id'];
    $nama_penerima = htmlspecialchars($_POST['nama_penerima']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $metode = $_POST['metode'];
    
    // Siapkan Waktu
    $tanggal = date("Y-m-d H:i:s");
    $estimasi_datang = date("Y-m-d H:i:s", strtotime("+3 days"));

    // 1. Ambil semua item di keranjang user
    $queryKeranjang = mysqli_query($conn, "SELECT k.*, p.harga, p.ketersediaan_stok 
                                          FROM keranjang k 
                                          JOIN produk p ON k.produk_id = p.id 
                                          WHERE k.user_id = '$user_id'");

    // Cek apakah keranjang kosong
    if(mysqli_num_rows($queryKeranjang) == 0) {
        echo "<script>alert('Keranjang Anda kosong!'); window.location='index.php';</script>";
        exit;
    }

    // Mulai Transaksi
    mysqli_begin_transaction($conn);

    try {
        while($item = mysqli_fetch_array($queryKeranjang)){
            $produk_id = $item['produk_id'];
            $jumlah = $item['jumlah'];
            $total_harga = $item['harga'] * $jumlah;

            // Cek stok
            if($item['ketersediaan_stok'] < $jumlah) {
                throw new Exception("Stok untuk salah satu produk tidak mencukupi.");
            }

            // 2. Masukkan ke tabel pesanan (Query lengkap dengan estimasi dan status)
            $insert = mysqli_query($conn, "INSERT INTO pesanan 
                (user_id, produk_id, jumlah, total_harga, metode_pembayaran, alamat, tanggal_pesanan, estimasi_datang, status_pesanan) 
                VALUES 
                ('$user_id', '$produk_id', '$jumlah', '$total_harga', '$metode', '$alamat', '$tanggal', '$estimasi_datang', 'Diproses')");

            if(!$insert) throw new Exception("Gagal menyimpan pesanan.");

            // 3. Kurangi stok produk
            $updateStok = mysqli_query($conn, "UPDATE produk SET ketersediaan_stok = ketersediaan_stok - $jumlah WHERE id = '$produk_id'");
            
            if(!$updateStok) throw new Exception("Gagal memperbarui stok.");
        }

        // 4. Kosongkan keranjang
        mysqli_query($conn, "DELETE FROM keranjang WHERE user_id = '$user_id'");

        // Jika semua oke, simpan permanen
        mysqli_commit($conn);

        echo "<script>alert('Pesanan berhasil dibuat! Estimasi tiba: " . date('d M Y', strtotime($estimasi_datang)) . "'); window.location='history.php';</script>";

    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua INSERT dan UPDATE stok di atas
        mysqli_rollback($conn);
        echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.location='checkout.php';</script>";
    }
}
?>
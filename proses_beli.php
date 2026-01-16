<?php
    session_start();
    require "koneksi.php";

    if(isset($_POST['beli'])){
        $user_id = $_SESSION['user_id'];
        $produk_id = $_POST['produk_id'];
        $jumlah = $_POST['jumlah'];

        // 1. Ambil detail produk (harga & cek stok)
        $queryProduk = mysqli_query($conn, "SELECT * FROM produk WHERE id='$produk_id'");
        $produk = mysqli_fetch_array($queryProduk);
        
        // Cek apakah stok mencukupi (keamanan tambahan)
        if($produk['ketersediaan_stok'] < $jumlah) {
            echo "<script>alert('Maaf, stok tidak mencukupi'); window.location='produk.php';</script>";
            exit;
        }

        $total_harga = $produk['harga'] * $jumlah;

        // 2. Ambil alamat terbaru pembeli dari tabel users
        $queryUser = mysqli_query($conn, "SELECT alamat FROM users WHERE id='$user_id'");
        $userData = mysqli_fetch_array($queryUser);
        $alamat_saat_ini = $userData['alamat'];

        // 3. Masukkan ke tabel pesanan (termasuk alamat pengiriman)
        // Pastikan Anda sudah menambahkan kolom 'alamat' di tabel pesanan
        $insert = mysqli_query($conn, "INSERT INTO pesanan (user_id, produk_id, jumlah, total_harga, alamat) 
                                      VALUES ('$user_id', '$produk_id', '$jumlah', '$total_harga', '$alamat_saat_ini')");
        
        if($insert) {
            // 4. Kurangi stok di database
            mysqli_query($conn, "UPDATE produk SET ketersediaan_stok = ketersediaan_stok - $jumlah WHERE id='$produk_id'");
            
            header("location: history.php");
        } else {
            echo "Gagal memproses pesanan: " . mysqli_error($conn);
        }
    }
?>
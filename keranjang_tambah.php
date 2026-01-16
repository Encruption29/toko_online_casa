<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['login_pembeli'])) {
    header("location: login.php");
    exit;
}

if(isset($_POST['add_to_cart'])){
    $user_id = $_SESSION['user_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Cek apakah barang sudah ada di keranjang user
    $cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE user_id='$user_id' AND produk_id='$produk_id'");
    
    if(mysqli_num_rows($cek) > 0){
        // Jika sudah ada, update jumlahnya
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE user_id='$user_id' AND produk_id='$produk_id'");
    } else {
        // Jika belum ada, insert baru
        mysqli_query($conn, "INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES ('$user_id', '$produk_id', '$jumlah')");
    }

    header("location: keranjang.php");
}
?>
<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['login_pembeli'])){
    header("location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Ambil data pesanan
    $query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id='$id_pesanan' AND user_id='$user_id'");
    $data = mysqli_fetch_array($query);

    if($data){
        // VALIDASI WAKTU (Keamanan Sisi Server)
        $waktu_pesan = strtotime($data['tanggal_pesanan']);
        $selisih = time() - $waktu_pesan;
        $batas_waktu = 3600; // 1 Jam

        if($selisih >= $batas_waktu) {
            echo "<script>alert('Gagal! Batas waktu pembatalan (1 jam) telah berakhir karena pesanan sedang dikirim.'); window.location='history.php';</script>";
            exit;
        }

        if($data['status_pesanan'] == 'Diproses') {
            // Mulai Transaksi Database
            mysqli_begin_transaction($conn);

            try {
                // 1. Kembalikan stok produk
                $produk_id = $data['produk_id'];
                $jumlah = $data['jumlah'];
                $updateStok = mysqli_query($conn, "UPDATE produk SET ketersediaan_stok = ketersediaan_stok + $jumlah WHERE id = '$produk_id'");
                
                if(!$updateStok) throw new Exception("Gagal mengembalikan stok.");

                // 2. Ubah status pesanan menjadi Dibatalkan
                $updateStatus = mysqli_query($conn, "UPDATE pesanan SET status_pesanan = 'Dibatalkan' WHERE id = '$id_pesanan'");
                
                if(!$updateStatus) throw new Exception("Gagal mengubah status pesanan.");

                mysqli_commit($conn);
                echo "<script>alert('Pesanan berhasil dibatalkan dan stok telah dikembalikan.'); window.location='history.php';</script>";

            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo "<script>alert('Gagal membatalkan: " . $e->getMessage() . "'); window.location='history.php';</script>";
            }
        } else {
            echo "<script>alert('Pesanan sudah tidak bisa dibatalkan.'); window.location='history.php';</script>";
        }
    } else {
        echo "<script>alert('Pesanan tidak ditemukan!'); window.location='history.php';</script>";
    }
} else {
    header("location: history.php");
}
?>
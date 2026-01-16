<?php
// File: koneksi.php

$conn = mysqli_connect("localhost", "root", "", "toko_online");

// --- TAMBAHKAN 2 BARIS INI ---
// 1. Atur Timezone PHP ke WIB
date_default_timezone_set('Asia/Jakarta');

// 2. Atur Timezone MySQL (Database) ke WIB (+07:00)
mysqli_query($conn, "SET time_zone = '+07:00'");
// -----------------------------

if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}
?>
<?php
    session_start();
    require "koneksi.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Tentang Kami</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require "navbar.php"; ?>

    <div class="container-fluid banner-produk d-flex align-items-center">
        <div class="container">
            <h1 class="text-white text-center">Tentang Kami</h1>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="container fs-5 text-center">
            <p>
                Kami adalah kelompok 5 mahasiswa Fasilkom Unilak yang berdedikasi untuk menciptakan toko online yang efisien, 
                modern, dan mudah digunakan untuk membantu pembeli mencari barang yang mereka inginkan. Aplikasi Toko Online 
                dikembangkan untuk memenuhi tugas mata kuliah Pengantar Teknologi Informasi. Sistem ini bertujuan untuk mempermudah 
                pengelolaan stok barang, kategori, serta pemantauan harga barang secara akurat.
            </p>
            <p>
                Aplikasi ini dirancang dengan antarmuka yang intuitif dan mudah digunakan, memungkinkan pengguna untuk menemukan 
                barang yang mereka inginkan dengan cepat dan efisien.
            </p>
            <p>
                Aji Royahya (2557201018) <br>
                Gunawan Gazali (2557201005) <br>
                Muhammad Ilham Ihsyahri (2557201006) <br>
             	Raihan Andriyas Prama (2557201004) <br>
                Zulfikar (2557201002)
            </p>
        </div>
    </div>

    <?php require "footer.php"; ?>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
</body>
</html>
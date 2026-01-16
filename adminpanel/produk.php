<?php
    require 'session.php';
    require '../koneksi.php';

    // Query untuk mengambil data produk dan nama kategorinya
    $queryProduk = mysqli_query($conn, "SELECT a.*, b.nama AS nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id");
    $jumlahProduk = mysqli_num_rows($queryProduk);

    // Fungsi untuk membuat nama file acak agar tidak bentrok
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        .no-decoration {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php require 'navbar.php'; ?>
    
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel" class="text-muted no-decoration"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
        
        <div class="my-5 col-12 col-md-6">
            <h3>Tambah Produk</h3>

            <?php
                if(isset($_POST['simpan'])){
                    $nama = htmlspecialchars($_POST['nama']);
                    $kategori = htmlspecialchars($_POST['kategori']);
                    $harga = htmlspecialchars($_POST['harga']);
                    $detail = htmlspecialchars($_POST['detail']);
                    $stok = htmlspecialchars($_POST['ketersediaan_stok']);

                    $target_dir = "../image/";
                    $nama_file = basename($_FILES["foto"]["name"]);
                    $target_file = $target_dir . $nama_file;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $image_size = $_FILES["foto"]["size"];
                    $random_name = generateRandomString(20);
                    $new_name = $random_name . "." . $imageFileType;

                    if($nama=='' || $kategori=='' || $harga==''){
                        echo '<div class="alert alert-warning mt-3" role="alert">Nama, Kategori dan Harga wajib diisi!</div>';
                    } else {
                        if($nama_file != ''){
                            if($image_size > 500000){
                                echo '<div class="alert alert-danger mt-3" role="alert">File tidak boleh lebih dari 500kb!</div>';
                            } else {
                                if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'gif' && $imageFileType != 'jpeg'){
                                    echo '<div class="alert alert-danger mt-3" role="alert">File wajib bertipe jpg, png, atau gif!</div>';
                                } else {
                                    // Pindahkan file ke folder target
                                    if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)){
                                        // Query Insert ke Database jika upload berhasil
                                        $queryTambah = mysqli_query($conn, "INSERT INTO produk (kategori_id, nama, harga, foto, detail, ketersediaan_stok) VALUES ('$kategori', '$nama', '$harga', '$new_name', '$detail', '$stok')");
                                        
                                        if($queryTambah){
                                            echo '<div class="alert alert-primary mt-3" role="alert">Produk Berhasil Tersimpan!</div>';
                                            echo '<meta http-equiv="refresh" content="1; url=produk.php" />';
                                        } else {
                                            echo mysqli_error($conn);
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger mt-3" role="alert">Gagal upload file ke server!</div>';
                                    }
                                }
                            }
                        }
                    }
                }
            ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control" autocomplete="off" required>
                </div>
                <div class="mb-3">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="">Pilih Satu</option>
                        <?php
                            $queryKategori = mysqli_query($conn, "SELECT * FROM kategori");
                            while($dataKategori = mysqli_fetch_array($queryKategori)){
                                echo "<option value='".$dataKategori['id']."'>".$dataKategori['nama']."</option>";
                            }
                        ?>
                    </select>
                </div> 
                <div class="mb-3">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" class="form-control" name="harga" required>
                </div>
                <div class="mb-3">
                    <label for="foto">Foto</label>
                    <input type="file" id="foto" class="form-control" name="foto" required>
                </div>
                <div class="mb-3">
                    <label for="detail">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="5" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="stok">Jumlah Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" value="<?php echo $produk['stok']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" name="simpan">Simpan</button>
            </form>
        </div>

        <div class="mt-3 mb-5">
            <h2>List Produk</h2>
            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($jumlahProduk == 0){
                                echo '<tr><td colspan="6" class="text-center">Data tidak tersedia</td></tr>';
                            } else {
                                $jumlah = 1;
                                while($data = mysqli_fetch_array($queryProduk)){
                        ?>
                                <tr>
                                    <td><?php echo $jumlah++; ?></td>
                                    <td><?php echo $data['nama']; ?></td>
                                    <td><?php echo $data['nama_kategori']; ?></td>
                                    <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $data['ketersediaan_stok'] > 5 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $data['ketersediaan_stok']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="produk_detail.php?p=<?php echo $data['id']; ?>" class="btn btn-info btn-sm text-white">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                        <?php
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
</body>
</html>
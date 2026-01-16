<?php
    require 'session.php';
    require '../koneksi.php';

    $id = $_GET['p'];

    $query = mysqli_query($conn, "SELECT a.*, b.nama AS nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id WHERE a.id='$id'");
    $data = mysqli_fetch_array($query);

    $queryKategori = mysqli_query($conn, "SELECT * FROM kategori WHERE id!='$data[kategori_id]'");

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
    <title>Produk Detail</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        .no-decoration {
            text-decoration: none;
        }
    </style>
</head>
<style>
    form div{ margin-bottom: 10px; }
</style>
<body>
    <?php require 'navbar.php'; ?>

    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel" class="text-muted no-decoration"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="produk.php" class="text-muted no-decoration">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Produk</li>
            </ol>
        </nav>

        <h2>Detail Produk</h2>

        <div class="col-12 col-md-6 mb-5">
            <form action="" method="post" enctype="multipart/form-data">
                <div>
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $data['nama']; ?>" class="form-control" autocomplete="off" required>
                </div>
                <div>
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="<?php echo $data['kategori_id']; ?>"><?php echo $data['nama_kategori']; ?></option>
                        <?php
                            while($dataKategori = mysqli_fetch_array($queryKategori)){
                                echo "<option value='".$dataKategori['id']."'>".$dataKategori['nama']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" value="<?php echo $data['harga']; ?>" name="harga" required>
                </div>
                <div>
                    <label for="currentFoto">Foto Produk Sekarang</label>
                    <div class="mb-2">
                        <?php if($data['foto'] == null || $data['foto'] == ''): ?>
                            <p class="text-danger italic">Foto tidak tersedia</p>
                        <?php else: ?>
                            <img src="../image/<?php echo $data['foto']; ?>" alt="Foto Produk" width="300px" class="img-thumbnail">
                        <?php endif; ?>
                    </div>
                    <label for="foto">Ganti Foto (Kosongkan jika tidak ingin ganti)</label>
                    <input type="file" id="foto" class="form-control" name="foto">
                </div>
                <div>
                    <label for="detail">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="10" class="form-control"><?php echo $data['detail']; ?></textarea>
                </div>
                <div>
                    <label for="ketersediaan_stok">Stok</label>
                    <input type="number" name="ketersediaan_stok" id="ketersediaan_stok" 
                           class="form-control" value="<?php echo $data['ketersediaan_stok']; ?>" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" name="update"><i class="fas fa-edit"></i> Update</button>
                    <button type="submit" class="btn btn-danger" name="hapus"><i class="fas fa-trash"></i> Hapus</button>
                </div>
            </form>

            <?php
                if(isset($_POST['update'])){
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
                        echo '<div class="alert alert-warning mt-3">Nama, Kategori dan Harga wajib diisi!</div>';
                    } else {
                        // 1. Update data teks terlebih dahulu
                        $queryUpdate = mysqli_query($conn, "UPDATE produk SET kategori_id='$kategori', nama='$nama', harga='$harga', detail='$detail', ketersediaan_stok='$stok' WHERE id=$id");

                        // 2. Jika ada file foto yang diupload
                        if($nama_file != ''){
                            if($image_size > 500000){
                                echo '<div class="alert alert-danger mt-3">File tidak boleh lebih dari 500kb!</div>';
                            } else {
                                if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'gif' && $imageFileType != 'jpeg'){
                                    echo '<div class="alert alert-danger mt-3">File wajib bertipe jpg, png, jpeg, atau gif!</div>';
                                } else {
                                    // Pindahkan file ke folder tujuan
                                    if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)){
                                        // Update nama file di database
                                        $queryUpdateFoto = mysqli_query($conn, "UPDATE produk SET foto='$new_name' WHERE id='$id'");
                                        
                                        if(!$queryUpdateFoto){
                                            echo '<div class="alert alert-danger mt-3">Gagal memperbarui foto di database: '.mysqli_error($conn).'</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Gagal mengunggah file ke folder tujuan. Pastikan folder "image" tersedia dan memiliki izin akses!</div>';
                                    }
                                }
                            }
                        }

                            // 3. Tampilkan pesan sukses jika query update teks berhasil
                            if($queryUpdate){
                                echo '<div class="alert alert-primary mt-3">Produk Berhasil Diupdate!</div>';
                                echo '<meta http-equiv="refresh" content="1; url=produk.php" />';
                            } else {
                                echo '<div class="alert alert-danger mt-3">Gagal update data: '.mysqli_error($conn).'</div>';
                            }
                        }
                    }

                    if(isset($_POST['hapus'])){
                        $queryHapus = mysqli_query($conn, "DELETE FROM produk WHERE id='$id'");
                        
                        if($queryHapus){
                            echo '<div class="alert alert-primary mt-3">Produk Berhasil Dihapus!</div>';
                            echo '<meta http-equiv="refresh" content="1; url=produk.php" />';
                        }
                    }
                ?>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
</body>
</html>
<?php
    require "session.php";
    require "../koneksi.php";

    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM kategori WHERE id='$id'");
    $data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Kategori</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        .no-decoration {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php require "navbar.php"; ?>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel" class="text-muted no-decoration"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="kategori.php" class="text-muted no-decoration">Kategori</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Kategori</li>
            </ol>
        </nav>

        <div class="col-12 col-md-6">
            <form action="" method="post">
                <div>
                    <label for="kategori">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-control" autocomplete="off" value="<?php echo $data['nama']; ?>">
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary" name="editBtn">Edit</button>
                    <button type="submit" class="btn btn-danger" name="deleteBtn">Delete</button>
                </div>
            </form>

            <?php
                // Logika Edit
                if(isset($_POST['editBtn'])){
                    $kategori = htmlspecialchars($_POST['kategori']);
                    if($data['nama'] == $kategori){
                        echo "<meta http-equiv='refresh' content='0; url=kategori.php' />";
                    } else {
                        $queryCheck = mysqli_query($conn, "SELECT * FROM kategori WHERE nama='$kategori'");
                        if(mysqli_num_rows($queryCheck) > 0){
                            echo "<div class='alert alert-warning mt-3'>Kategori sudah ada</div>";
                        } else {
                            $queryUpdate = mysqli_query($conn, "UPDATE kategori SET nama='$kategori' WHERE id='$id'");
                            if($queryUpdate){
                                echo "<div class='alert alert-primary mt-3'>Kategori berhasil di-update</div>";
                                echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                            }
                        }
                    }
                }

                // Logika Delete
                if(isset($_POST['deleteBtn'])){
                    // 1. Cek apakah ada produk yang masih menggunakan kategori ini
                    $queryCheckProduk = mysqli_query($conn, "SELECT id FROM produk WHERE kategori_id='$id'");
                    $dataCount = mysqli_num_rows($queryCheckProduk);

                    if($dataCount > 0){
                    // 2. Jika ada produk, tampilkan pesan error
                    echo "<div class='alert alert-danger mt-3'>
                            Kategori tidak dapat dihapus karena masih digunakan oleh $dataCount produk. 
                            Silakan hapus atau pindahkan produk terkait terlebih dahulu.
                        </div>";
                } else {
                    // 3. Jika tidak ada produk, jalankan perintah hapus
                    $queryDelete = mysqli_query($conn, "DELETE FROM kategori WHERE id='$id'");
                    
                    if($queryDelete){
                        echo "<div class='alert alert-primary mt-3'>Kategori berhasil dihapus</div>";
                        echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                    } else {
                        // Menampilkan error mysqli jika terjadi masalah lain
                        echo mysqli_error($conn);
                    }
                }
            }
            ?>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
</body>
</html>
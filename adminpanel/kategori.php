<?php
    require "session.php";
    require "../koneksi.php";

    $queryKategori = mysqli_query($conn, "SELECT * FROM kategori");
    $jumlahKategori = mysqli_num_rows($queryKategori);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kategori</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        .no-decoration {
            text-decoration: none;
        }
    </style>
    <?php require "navbar.php"; ?>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../adminpanel" class="text-muted no-decoration"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori</li>
            </ol>
        </nav>

        <div class="my-5 col-12 col-md-6">
            <h3>Tambah Kategori</h3>
            <form action="" method="post">
                <div>
                    <label for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" placeholder="input nama kategori" class="form-control mt-2">
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit" name="simpan_kategori">Simpan</button>
                </div>
            </form>

            <?php
                if(isset($_POST['simpan_kategori'])){
                    $kategori = htmlspecialchars($_POST['kategori']);
                    $queryExist = mysqli_query($conn, "SELECT nama FROM kategori WHERE nama='$kategori'");
                    $jumlahDataKategoriBaru = mysqli_num_rows($queryExist);

                    if($jumlahDataKategoriBaru > 0){
                        echo "<div class='alert alert-warning mt-3'>Kategori sudah ada</div>";
                    } else {
                        $querySimpan = mysqli_query($conn, "INSERT INTO kategori (nama) VALUES ('$kategori')");
                        if($querySimpan){
                            echo "<div class='alert alert-primary mt-3'>Kategori berhasil tersimpan</div>";
                            echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                        }
                    }
                }
            ?>
        </div>

        <div class="mt-3">
            <h2>List Kategori</h2>
            <div class="table-responsive mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($jumlahKategori==0){
                                echo "<tr><td colspan='3' class='text-center'>Data kategori tidak tersedia</td></tr>";
                            } else {
                                $number = 1;
                                while($data = mysqli_fetch_array($queryKategori)){
                                    echo "<tr>
                                            <td>$number</td>
                                            <td>$data[nama]</td>
                                            <td>
                                                <a href='kategori_detail.php?id=$data[id]' class='btn btn-info'><i class='fas fa-search'></i> Detail</a>
                                            </td>
                                          </tr>";
                                    $number++;
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
<?php
    session_start();
    require "koneksi.php";

    // Proteksi: Jika belum login, dialihkan ke login.php
    if(!isset($_SESSION['login_pembeli'])){ 
        header("location: login.php"); 
        exit; 
    }

    $user_id = $_SESSION['user_id'];

    // Ambil data user terbaru dari database
    $queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
    $userData = mysqli_fetch_array($queryUser);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Profil Saya</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require "navbar.php"; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-8 offset-lg-2">
                <h2 class="mb-4"><i class="fas fa-user-cog text-primary"></i> Pengaturan Profil</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-header warna1 text-white">
                        <h5 class="mb-0">Informasi Pribadi</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $userData['username']; ?>" readonly>
                                <div class="form-text text-muted small">Username tidak dapat diubah.</div>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" id="nama" class="form-control" value="<?php echo $userData['nama']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label fw-bold">Alamat Pengiriman</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" required><?php echo $userData['alamat']; ?></textarea>
                            </div>
                            <button type="submit" name="update_profil" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </form>

                        <?php
                        if(isset($_POST['update_profil'])){
                            $nama = htmlspecialchars($_POST['nama']);
                            $alamat = htmlspecialchars($_POST['alamat']);

                            $update = mysqli_query($conn, "UPDATE users SET nama='$nama', alamat='$alamat' WHERE id='$user_id'");
                            
                            if($update){
                                echo "<div class='alert alert-success mt-3'>Profil berhasil diperbarui!</div>";
                                echo "<meta http-equiv='refresh' content='1'>";
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark">Ganti Password</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="pass_baru" class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="pass_baru" id="pass_baru" class="form-control" placeholder="Masukkan password baru" required>
                            </div>
                            <div class="mb-3">
                                <label for="konfirmasi_pass" class="form-label fw-bold">Konfirmasi Password Baru</label>
                                <input type="password" name="konfirmasi_pass" id="konfirmasi_pass" class="form-control" placeholder="Ulangi password baru" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-warning px-4">Perbarui Password</button>
                        </form>

                        <?php
                        if(isset($_POST['update_password'])){
                            $pass_baru = $_POST['pass_baru'];
                            $konfirmasi = $_POST['konfirmasi_pass'];

                            if($pass_baru === $konfirmasi){
                                $password_hashed = password_hash($pass_baru, PASSWORD_DEFAULT);
                                $updatePass = mysqli_query($conn, "UPDATE users SET password='$password_hashed' WHERE id='$user_id'");
                                
                                if($updatePass){
                                    echo "<div class='alert alert-success mt-3'>Password berhasil diganti!</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger mt-3'>Konfirmasi password tidak cocok!</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="text-decoration-none"><i class="fas fa-chevron-left"></i> Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>

    <?php require "footer.php"; ?>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
</body>
</html>
<?php
    require "koneksi.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Daftar Akun</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .background {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('css/bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh; /* Menggunakan min-height agar konten panjang tidak terpotong */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .register-box {
            width: 500px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .btn-primary {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="d-flex flex-column align-items-center">
            <div class="register-box">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">Daftar Akun</h3>
                    <p class="text-muted">Lengkapi data untuk mulai belanja</p>
                </div>

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="nama" class="form-label small fw-bold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama lengkap Anda" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Buat username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Buat password kuat" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label small fw-bold">Alamat Pengiriman</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Alamat lengkap pengiriman" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 mt-2">Daftar Sekarang</button>
                </form>

                <p class="mt-4 text-center mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login di sini</a></p>
            </div>

            <div class="mt-3" style="width: 500px;">
                <?php
                    if(isset($_POST['register'])){
                        $nama = htmlspecialchars($_POST['nama']);
                        $username = htmlspecialchars($_POST['username']);
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $alamat = htmlspecialchars($_POST['alamat']);

                        $queryCheck = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
                        
                        if(mysqli_num_rows($queryCheck) > 0){
                            echo "<div class='alert alert-danger text-center shadow-sm'>Username sudah digunakan!</div>";
                        } else {
                            $queryRegister = mysqli_query($conn, "INSERT INTO users (nama, username, password, alamat) VALUES ('$nama', '$username', '$password', '$alamat')");
                            
                            if($queryRegister){
                                echo "<div class='alert alert-success text-center shadow-sm'>Pendaftaran berhasil! Silahkan <a href='login.php' class='fw-bold'>Login</a>.</div>";
                            } else {
                                echo "<div class='alert alert-danger text-center shadow-sm'>Terjadi kesalahan teknis.</div>";
                            }
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
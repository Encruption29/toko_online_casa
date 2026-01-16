<?php
    session_start();
    require "koneksi.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Login Pembeli</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 450px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
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
    </style>
</head>
<body>
    <div class="background">
        <div class="d-flex flex-column align-items-center">
            <div class="login-box shadow">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                    <h3 class="fw-bold">Selamat Datang</h3>
                    <p class="text-muted">Silahkan login ke akun Anda</p>
                </div>

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit" name="loginbtn">Masuk Sekarang</button>
                </form>
                
                <p class="mt-4 text-center">Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold">Daftar Gratis</a></p>
            </div>

            <div class="mt-3" style="width: 450px;">
                <?php
                    if(isset($_POST['loginbtn'])) {
                        $username = htmlspecialchars($_POST['username']);
                        $password = htmlspecialchars($_POST['password']);

                        $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
                        $countdata = mysqli_num_rows($query);
                        $data = mysqli_fetch_array($query);

                        if($countdata > 0) {
                            if(password_verify($password, $data['password'])) {
                                $_SESSION['username'] = $data['username'];
                                $_SESSION['user_id'] = $data['id'];
                                $_SESSION['login_pembeli'] = true;
                                header("Location: ../toko_online");
                            } else {
                                echo "<div class='alert alert-danger text-center shadow-sm animate__animated animate__shakeX'>Password Anda salah!</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger text-center shadow-sm'>Username tidak ditemukan!</div>";
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
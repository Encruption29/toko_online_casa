<?php
    session_start();
    require "koneksi.php";

    // 1. Pastikan Timezone Tetap Ada
    date_default_timezone_set('Asia/Jakarta');

    if(!isset($_SESSION['login_pembeli'])){ 
        header("location: login.php"); 
        exit; 
    }

    $user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online | Riwayat Belanja</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table img { border-radius: 8px; }
        .trx-id { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #0d6efd; }
        .card { border: none; border-radius: 15px; }
        .status-proses { background-color: #e3f2fd; color: #0d6efd; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-batal { background-color: #ffebee; color: #c62828; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    <?php require "navbar.php"; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active">Riwayat Belanja</li>
                    </ol>
                </nav>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold"><i class="fas fa-history text-primary me-2"></i> Riwayat Belanja</h2>
                    <span class="badge bg-primary rounded-pill px-3 py-2">Total Transaksi: 
                        <?php 
                        $res = mysqli_query($conn, "SELECT id FROM pesanan WHERE user_id='$user_id'");
                        echo mysqli_num_rows($res);
                        ?>
                    </span>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4">Detail Produk</th>
                                        <th>Pengiriman & Estimasi</th>
                                        <th>Waktu Transaksi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Total</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT p.*, pr.nama, pr.foto 
                                                                FROM pesanan p 
                                                                JOIN produk pr ON p.produk_id = pr.id 
                                                                WHERE p.user_id='$user_id' 
                                                                ORDER BY p.tanggal_pesanan DESC");
                                    
                                    if(mysqli_num_rows($query) == 0): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Belum ada transaksi apapun</h5>
                                                <a href="produk.php" class="btn btn-primary mt-2">Belanja Sekarang</a>
                                            </td>
                                        </tr>
                                    <?php else: 
                                        while($data = mysqli_fetch_array($query)): 
                                            // --- LOGIKA WAKTU ---
                                            $id_pesanan = $data['id'];
                                            $waktu_pesan = strtotime($data['tanggal_pesanan']);
                                            
                                            // Estimasi tiba dianggap selesai pada jam 00:00:00 hari tersebut
                                            $estimasi_tiba = strtotime($data['estimasi_datang']); 
                                            $waktu_sekarang = time(); 
                                            $selisih_detik = $waktu_sekarang - $waktu_pesan;
                                            $batas_waktu_batal = 3600; // 1 Jam

                                            // --- [BARU] LOGIKA AUTO UPDATE DATABASE ---
                                            // Kita cek apakah status di database perlu diperbarui?
                                            $status_db = $data['status_pesanan'];
                                            $status_baru = $status_db; // Default sama

                                            // Cek 1: Apakah harus berubah jadi SELESAI?
                                            if ($status_db != 'Selesai' && $status_db != 'Dibatalkan' && $waktu_sekarang >= $estimasi_tiba) {
                                                $status_baru = 'Selesai';
                                                mysqli_query($conn, "UPDATE pesanan SET status_pesanan='Selesai' WHERE id='$id_pesanan'");
                                            }
                                            // Cek 2: Apakah harus berubah jadi DIKIRIM (lewat 1 jam)?
                                            // Syarat: Status DB masih 'Diproses', belum Selesai, dan waktu > 1 jam
                                            elseif ($status_db == 'Diproses' && $selisih_detik > $batas_waktu_batal) {
                                                // Catatan: Biasanya 'Dikirim' diinput admin/resi, tapi ini mengikuti logika otomatis Anda
                                                // Kita update tampilan saja atau update DB juga?
                                                // Agar konsisten, kita biarkan status DB 'Diproses' tapi tampilannya 'Dikirim' (seperti sebelumnya)
                                                // KECUALI Anda ingin status 'Dikirim' juga permanen di DB:
                                                // mysqli_query($conn, "UPDATE pesanan SET status_pesanan='Dikirim' WHERE id='$id_pesanan'");
                                            }

                                            // Update variabel $data lokal agar tampilan langsung berubah tanpa refresh ulang
                                            if ($status_baru != $status_db) {
                                                $data['status_pesanan'] = $status_baru;
                                            }
                                            
                                            // --- LOGIKA TAMPILAN FINAL ---
                                            // Variabel ini untuk menentukan badge warna apa yang muncul
                                            $tampilan_status = $data['status_pesanan'];
                                            
                                            // Jika status DB masih 'Diproses' tapi sudah lewat 1 jam, TAMPILKAN sebagai Dikirim
                                            if ($tampilan_status == 'Diproses' && $selisih_detik > $batas_waktu_batal) {
                                                $tampilan_status = 'Dikirim';
                                            }
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="image/<?php echo $data['foto']; ?>" class="me-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo $data['nama']; ?></div>
                                                        <small class="trx-id">#TRX-<?php echo $data['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="mb-1 small">
                                                    <i class="fas fa-truck text-muted me-1"></i> Tiba: <strong><?php echo date('d M Y', strtotime($data['estimasi_datang'])); ?></strong>
                                                </div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">
                                                    <i class="fas fa-map-marker-alt me-1"></i> <?php echo $data['alamat']; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small><?php echo date('d/m/y H:i', $waktu_pesan); ?> WIB</small><br>
                                                <span class="badge bg-light text-dark border" style="font-size: 0.65rem;"><?php echo $data['metode_pembayaran']; ?></span>
                                            </td>
                                            
                                            <td class="text-center">
                                                <?php if($tampilan_status == 'Selesai'): ?>
                                                    <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Selesai</span>
                                                <?php elseif($tampilan_status == 'Dibatalkan'): ?>
                                                    <span class="status-batal"><i class="fas fa-times-circle me-1"></i> Batal</span>
                                                <?php elseif($tampilan_status == 'Dikirim'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-shipping-fast me-1"></i> Dikirim</span>
                                                <?php else: ?>
                                                    <span class="status-proses"><i class="fas fa-sync-alt fa-spin me-1"></i> Diproses</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-end pe-4 fw-bold text-primary">
                                                Rp <?php echo number_format($data['total_harga']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?php 
                                                // Tombol Batal HANYA jika status DB 'Diproses' DAN waktu < 1 jam
                                                if($data['status_pesanan'] == 'Diproses' && $selisih_detik < $batas_waktu_batal): 
                                                ?>
                                                    <a href="batal_pesanan.php?id=<?php echo $data['id']; ?>" 
                                                       class="btn btn-outline-danger btn-sm" 
                                                       onclick="return confirm('Batalkan pesanan ini? Stok akan dikembalikan otomatis.')">
                                                        Batal
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light border" disabled>-</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require "footer.php"; ?>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
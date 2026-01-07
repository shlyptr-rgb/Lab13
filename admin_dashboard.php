<?php
session_start();
require 'koneksi.php';

// Satpam: Cek apakah yang login beneran admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil semua data sneakers dari database
$stmt = $pdo->query("SELECT * FROM sneakers ORDER BY id DESC");
$sneakers = $stmt->fetchAll();

// --- LOGIK STATISTIK RINGKAS ---
$total_koleksi = count($sneakers);
$stok_menipis = 0;
foreach ($sneakers as $s) {
    if ($s['stok'] <= 3) $stok_menipis++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sneaker Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { height: 100vh; background: #1a1c1e; color: white; position: fixed; width: 260px; padding: 25px; transition: 0.3s; }
        .main-content { margin-left: 260px; padding: 40px; }
        
        /* Card Styles */
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card { padding: 25px; border-radius: 20px; color: white; border: none; }
        
        /* Sidebar Nav */
        .nav-link { color: rgba(255,255,255,0.6) !important; padding: 12px 20px; border-radius: 12px; margin-bottom: 8px; font-weight: 600; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff !important; }
        .nav-link.active { background: #0d6efd; color: #fff !important; shadow: 0 4px 12px rgba(13, 110, 253, 0.3); }
        
        /* Table Styles */
        .table thead th { background: #f8f9fa; color: #6c757d; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border: none; padding: 15px; }
        .table tbody td { padding: 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        .table img { border-radius: 12px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        .btn-logout { background: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; border-radius: 12px; padding: 12px; width: 100%; font-weight: 700; transition: 0.3s; }
        .btn-logout:hover { background: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <h4 class="fw-800 text-primary mb-5">SNEAKER <span class="text-white">VAULT</span></h4>
    <div class="mb-4">
        <p class="small text-secondary mb-1">ADMINISTRATOR</p>
        <h6 class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></h6>
    </div>
    <hr class="opacity-10">
    <nav class="nav flex-column">
        <a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-grid-2 me-2"></i> Dashboard</a>
        <a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-bag me-2"></i> Pesanan</a>
        <a class="nav-link" href="index.php"><i class="fas fa-store me-2"></i> Ke Toko</a>
    </nav>
    <div style="position: absolute; bottom: 30px; width: 210px;">
        <a href="logout.php" class="btn-logout text-decoration-none d-block text-center">
            <i class="fas fa-sign-out-alt me-2"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark mb-1">Halo, <?= explode(' ', $_SESSION['username'])[0] ?>! 👋</h2>
            <p class="text-secondary">Kelola stok sepatu original lo di sini.</p>
        </div>
        <a href="tambah_produk.php" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
            <i class="fas fa-plus me-2"></i> TAMBAH PRODUK
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-custom p-4 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="fas fa-boxes-stacked text-primary fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-secondary mb-0 small fw-bold">TOTAL MODEL</p>
                        <h3 class="fw-800 mb-0 text-dark"><?= $total_koleksi ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-4 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-secondary mb-0 small fw-bold">STOK MENIPIS (<=3)</p>
                        <h3 class="fw-800 mb-0 text-dark"><?= $stok_menipis ?> <small class="text-secondary" style="font-size: 14px">Model</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Preview</th>
                            <th>Info Sepatu</th>
                            <th>Harga Satuan</th>
                            <th>Status Stok</th>
                            <th>Size</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sneakers) > 0): ?>
                            <?php foreach ($sneakers as $s): ?>
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <img src="<?= htmlspecialchars($s['gambar']) ?>" width="70" height="70" alt="shoes">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($s['nama_sneaker']) ?></div>
                                    <span class="badge bg-light text-secondary border"><?= htmlspecialchars($s['brand'] ?? 'Sneakers') ?></span>
                                </td>
                                <td class="fw-bold">Rp<?= number_format($s['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if($s['stok'] <= 0): ?>
                                        <span class="badge bg-danger rounded-pill px-3">HABIS</span>
                                    <?php elseif($s['stok'] <= 3): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3">SISA <?= $s['stok'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><?= $s['stok'] ?> Ready</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-muted font-monospace"><?= htmlspecialchars($s['ukuran']) ?></span></td>
                                <td class="text-center pe-4">
                                    <div class="btn-group shadow-sm rounded-3">
                                        <a href="edit_produk.php?id=<?= $s['id'] ?>" class="btn btn-white btn-sm px-3 border-end" title="Edit">
                                            <i class="fas fa-pen text-warning"></i>
                                        </a>
                                        <a href="hapus_produk.php?id=<?= $s['id'] ?>" class="btn btn-white btn-sm px-3" title="Hapus" onclick="return confirm('Yakin mau hapus sepatu ini dari rak?')">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="opacity-25 mb-3"><br>
                                    <span class="text-secondary">Belum ada koleksi sepatu di rak lo.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
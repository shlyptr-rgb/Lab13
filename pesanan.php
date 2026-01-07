<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}

$stmt = $pdo->query("SELECT * FROM pesanan ORDER BY tanggal DESC");
$semua_pesanan = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pesanan - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i> Daftar Pesanan Masuk</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <div class="card shadow border-0 rounded-3">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Produk</th>
                            <th>Total Bayar</th>
                            <th>Alamat & WA</th>
                            <th>No. Resi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($semua_pesanan as $p): ?>
                        <tr>
                            <td><?= date('d/m/y H:i', strtotime($p['tanggal'])) ?></td>
                            <td class="fw-bold"><?= $p['nama_pembeli'] ?></td>
                            <td><?= $p['nama_sepatu'] ?> (Size <?= $p['ukuran'] ?>) x<?= $p['jumlah'] ?></td>
                            <td class="text-success fw-bold">Rp<?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                            <td>
                                <small>WA: <?= $p['whatsapp'] ?></small><br>
                                <small class="text-muted"><?= $p['alamat'] ?></small>
                            </td>
                            <td><span class="badge bg-primary"><?= $p['no_resi'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
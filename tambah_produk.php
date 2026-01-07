<?php
session_start();
require 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_sneaker'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $ukuran = $_POST['ukuran'];

    // Logika Upload Gambar
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
    
    // Bikin nama file baru biar gak bentrok (Contoh: sneakers_12345.jpg)
    $nama_baru = "sneakers_" . time() . "." . $ekstensi;
    $path = "img/" . $nama_baru;

    if (move_uploaded_file($tmp_file, $path)) {
        $sql = "INSERT INTO sneakers (nama_sneaker, harga, stok, ukuran, gambar) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $harga, $stok, $ukuran, $path]);

        echo "<script>alert('Sepatu Berhasil Ditambah!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal upload gambar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk - Sneaker Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; padding: 50px; }
        .form-container { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
    </style>
</head>
<body>

<div class="form-container">
    <h3 class="fw-bold mb-4 text-center">TAMBAH KOLEKSI BARU</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nama Sneaker</label>
            <input type="text" name="nama_sneaker" class="form-control" placeholder="Contoh: Jordan 1 High" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" placeholder="1500000" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" placeholder="10" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Ukuran (Pisahkan dengan koma)</label>
            <input type="text" name="ukuran" class="form-control" placeholder="40, 41, 42, 43" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Upload Foto Sepatu</label>
            <input type="file" name="gambar" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold py-2">SIMPAN KE DATABASE</button>
        <a href="admin_dashboard.php" class="btn btn-light w-100 mt-2 text-decoration-none text-center">Batal</a>
    </form>
</div>

</body>
</html>
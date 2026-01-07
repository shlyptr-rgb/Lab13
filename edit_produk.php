<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM sneakers WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (isset($_POST['edit'])) {
    $nama = $_POST['nama_sneaker'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $ukuran = $_POST['ukuran'];
    $gambar_lama = $_POST['gambar_lama'];

    // Cek apakah admin upload gambar baru
    if ($_FILES['gambar']['name'] !== "") {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $path = "img/" . "sneakers_" . time() . "_" . $nama_file;
        
        move_uploaded_file($tmp_file, $path);
        // Hapus gambar lama biar gak nyampah
        if (file_exists($gambar_lama)) { unlink($gambar_lama); }
    } else {
        $path = $gambar_lama; // Tetap pakai gambar lama
    }

    $sql = "UPDATE sneakers SET nama_sneaker=?, harga=?, stok=?, ukuran=?, gambar=? WHERE id=?";
    $pdo->prepare($sql)->execute([$nama, $harga, $stok, $ukuran, $path, $id]);

    echo "<script>alert('Data Berhasil Diupdate!'); window.location='admin_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk - Sneaker Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container shadow bg-white p-4 rounded-4" style="max-width: 600px;">
    <h3 class="fw-bold mb-4">Edit Produk</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="gambar_lama" value="<?= $s['gambar'] ?>">
        
        <div class="mb-3">
            <label>Nama Sneaker</label>
            <input type="text" name="nama_sneaker" class="form-control" value="<?= $s['nama_sneaker'] ?>" required>
        </div>
        <div class="row mb-3">
            <div class="col"><label>Harga</label><input type="number" name="harga" class="form-control" value="<?= $s['harga'] ?>" required></div>
            <div class="col"><label>Stok</label><input type="number" name="stok" class="form-control" value="<?= $s['stok'] ?>" required></div>
        </div>
        <div class="mb-3">
            <label>Ukuran</label>
            <input type="text" name="ukuran" class="form-control" value="<?= $s['ukuran'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Gambar Saat Ini</label><br>
            <img src="<?= $s['gambar'] ?>" width="100" class="mb-2 rounded border">
            <input type="file" name="gambar" class="form-control">
            <small class="text-muted text-italic">*Kosongkan jika tidak ingin ganti gambar</small>
        </div>
        <button type="submit" name="edit" class="btn btn-warning w-100 fw-bold">UPDATE DATA</button>
        <a href="admin_dashboard.php" class="btn btn-link w-100 mt-2">Batal</a>
    </form>
</div>
</body>
</html>
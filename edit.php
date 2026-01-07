<?php
require 'koneksi.php';

// 1. Ambil ID dari URL
$id = $_GET['id'];

// 2. Tarik data lama dari database berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM sneakers WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

// 3. Jika tombol update ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_sneaker'];
    $brand = $_POST['brand'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $ukuran = $_POST['ukuran'];
    $gambar = $_POST['gambar'];

    try {
        $update = $pdo->prepare("UPDATE sneakers SET nama_sneaker=?, brand=?, harga=?, stok=?, ukuran=?, gambar=? WHERE id=?");
        $update->execute([$nama, $brand, $harga, $stok, $ukuran, $gambar, $id]);
        
        header("Location: index.php?pesan=update_berhasil");
        exit();
    } catch (PDOException $e) {
        die("Gagal update bro: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Sepatu | Sneaker Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0f1012; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-edit { background: #1a1c1e; border: 1px solid #333; border-radius: 20px; padding: 30px; margin-top: 50px; }
        .form-control { background: #212428 !important; border: 1px solid #333 !important; color: white !important; padding: 12px; }
        .form-control:focus { border-color: #007bff !important; box-shadow: none; }
        label { font-size: 0.85rem; color: #888; margin-bottom: 5px; margin-left: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-edit shadow-lg">
                <h4 class="fw-bold mb-4 text-primary text-center">EDIT DETAIL SEPATU</h4>
                
                <form action="" method="POST">
                    <div class="mb-3">
                        <label>Nama Sneaker</label>
                        <input type="text" name="nama_sneaker" class="form-control" value="<?= $s['nama_sneaker'] ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Brand</label>
                            <input type="text" name="brand" class="form-control" value="<?= $s['brand'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="<?= $s['harga'] ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?= $s['stok'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Ukuran (Size)</label>
                            <input type="text" name="ukuran" class="form-control" value="<?= $s['ukuran'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label>Link Gambar URL</label>
                        <input type="text" name="gambar" class="form-control" value="<?= $s['gambar'] ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <a href="index.php" class="btn btn-outline-secondary w-50 fw-bold py-3 rounded-pill">BATAL</a>
                        <button type="submit" class="btn btn-primary w-50 fw-bold py-3 rounded-pill">SIMPAN PERUBAHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
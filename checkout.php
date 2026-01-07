<?php
require 'koneksi.php';
session_start();

// Ambil ID sepatu (Support routing tanpa .php)
if (!isset($_GET['id'])) {
    header("Location: index");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM sneakers WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) { die("Sepatu tidak ditemukan!"); }

// Jika tombol "Konfirmasi Bayar" diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pembeli = $_POST['nama_pembeli'];
    $hp = $_POST['hp'];
    $alamat = $_POST['alamat'];
    $ukuran_pilih = $_POST['ukuran_pilih'];
    $jumlah_beli = (int)$_POST['jumlah_beli'];
    $metode = $_POST['metode'];
    $total_harga = $s['harga'] * $jumlah_beli;

    // Proteksi stok
    if ($jumlah_beli > $s['stok']) {
        echo "<script>alert('Waduh, stok nggak cukup bro!'); window.history.back();</script>";
        exit;
    }

    // 1. Kurangi Stok
    $update = $pdo->prepare("UPDATE sneakers SET stok = stok - ? WHERE id = ?");
    $update->execute([$jumlah_beli, $id]);

    // 2. No Resi Otomatis
    $no_resi = "JNT" . strtoupper(substr(md5(time()), 0, 10));

    // 3. Simpan Pesanan
    $sql_pesanan = "INSERT INTO pesanan (nama_pembeli, whatsapp, alamat, nama_sepatu, ukuran, jumlah, total_bayar, metode_bayar, no_resi) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_pesanan = $pdo->prepare($sql_pesanan);
    $stmt_pesanan->execute([
        $nama_pembeli, $hp, $alamat, $s['nama_sneaker'], $ukuran_pilih, $jumlah_beli, $total_harga, $metode, $no_resi
    ]);

    // 4. Struk Digital (Receipt View)
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <title>Nota Pesanan - #SV<?= time() ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: #e9ecef; font-family: 'Courier New', Courier, monospace; color: #000; padding: 40px 10px; }
            .struk { background: white; max-width: 480px; margin: 0 auto; padding: 35px; border-radius: 4px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; }
            .struk::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: #3d8bff; }
            hr { border-top: 1px dashed #000; opacity: 0.3; margin: 15px 0; }
            .shipping-box { background: #f8f9fa; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin: 20px 0; }
            @media print { .d-print-none { display: none; } body { background: white; padding: 0; } .struk { box-shadow: none; max-width: 100%; } }
        </style>
    </head>
    <body>
        <div class="struk">
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-0">SNEAKER VAULT</h3>
                <p class="small text-muted">Authentic Kicks Collection</p>
            </div>
            
            <div class="small">
                <div class="d-flex justify-content-between"><span>No. Invoice</span><strong>#SV<?= strtoupper(substr(uniqid(), 7)) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Tanggal</span><strong><?= date('d/m/Y H:i') ?></strong></div>
            </div>
            
            <hr>
            
            <div class="mb-3 small">
                <p class="mb-1 uppercase"><strong>Customer:</strong> <?= htmlspecialchars($nama_pembeli) ?></p>
                <p class="mb-1"><strong>WhatsApp:</strong> <?= htmlspecialchars($hp) ?></p>
                <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($alamat) ?></p>
            </div>
            
            <hr>
            
            <table class="w-100 small mb-3">
                <tr>
                    <td class="py-2"><strong><?= $s['nama_sneaker'] ?></strong><br><span class="text-muted">Size: <?= $ukuran_pilih ?> (<?= $jumlah_beli ?>x)</span></td>
                    <td class="text-end py-2">Rp<?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
            </table>
            
            <hr>
            
            <div class="d-flex justify-content-between h5 fw-bold">
                <span>TOTAL</span>
                <span>Rp<?= number_format($total_harga, 0, ',', '.') ?></span>
            </div>
            <p class="small mt-2"><strong>Payment:</strong> <?= $metode ?></p>
            
            <div class="shipping-box small">
                <div class="d-flex justify-content-between mb-1"><span>Courier</span><strong>J&T Express</strong></div>
                <div class="d-flex justify-content-between mb-1"><span>Tracking ID</span><strong><?= $no_resi ?></strong></div>
                <div class="d-flex justify-content-between text-primary"><span>Status</span><strong>Processing</strong></div>
            </div>

            <div class="text-center mt-4">
                <p class="mb-1 fw-bold">Thank you for your purchase!</p>
                <p class="x-small text-muted">Please save this receipt as proof of transaction.</p>
            </div>

            <div class="mt-4 d-print-none d-grid gap-2">
                <button onclick="window.print()" class="btn btn-primary fw-bold">PRINT / SAVE PDF</button>
                <a href="index" class="btn btn-outline-dark">BACK TO SHOP</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | SNEAKER VAULT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #0b0c0e; color: white; font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px 0; }
        .card-checkout { background: #141619; border: 1px solid rgba(255,255,255,0.05); border-radius: 28px; padding: 35px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .product-info { background: rgba(255,255,255,0.03); border-radius: 20px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .form-label { font-size: 0.75rem; font-weight: 700; color: #8a8d91; text-transform: uppercase; letter-spacing: 1px; }
        .form-control, .form-select { background: #1c1f23 !important; border: 1px solid #2d3136 !important; color: white !important; border-radius: 12px; padding: 12px 15px; }
        .form-control:focus { border-color: #3d8bff !important; box-shadow: none; }
        .btn-confirm { background: #3d8bff; border: none; border-radius: 15px; padding: 16px; font-weight: 800; letter-spacing: 1px; transition: 0.3s; }
        .btn-confirm:hover { background: #2b74e6; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(61, 139, 255, 0.3); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card-checkout">
                <h3 class="fw-800 mb-4">CHECKOUT</h3>
                
                <div class="product-info d-flex align-items-center gap-4 mb-4">
                    <div class="bg-white p-2 rounded-4" style="width: 100px; height: 100px; display: flex; align-items: center;">
                        <img src="<?= $s['gambar'] ?>" class="img-fluid object-fit-contain">
                    </div>
                    <div>
                        <h5 class="mb-1 fw-700"><?= $s['nama_sneaker'] ?></h5>
                        <p class="text-primary fw-800 mb-1">Rp<?= number_format($s['harga'], 0, ',', '.') ?></p>
                        <span class="badge bg-white bg-opacity-10 text-white-50 small">Stock: <?= $s['stok'] ?> pairs</span>
                    </div>
                </div>

                <form action="checkout?id=<?= $id ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="nama_pembeli" class="form-control" placeholder="Sherly Vault" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">WhatsApp Number</label>
                            <input type="text" name="hp" class="form-control" placeholder="08xxxx" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Size</label>
                            <select name="ukuran_pilih" class="form-select" required>
                                <option value="" disabled selected>Size</option>
                                <?php 
                                $sizes = explode(',', str_replace(' ', '', $s['ukuran']));
                                foreach($sizes as $sz) { if(!empty($sz)) echo "<option value='$sz'>Size $sz</option>"; }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="jumlah_beli" class="form-control" value="1" min="1" max="<?= $s['stok'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Full address..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Payment Method</label>
                        <select name="metode" class="form-select">
                            <option>COD (Bayar di Tempat)</option>
                            <option>BCA Transfer - 12345678 (A/N Sherly)</option>
                            <option>DANA / OVO - 08123456789</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-confirm btn-primary w-100 mb-3">CONFIRM ORDER</button>
                    <div class="text-center">
                        <a href="index" class="text-muted small text-decoration-none">← Back to Gallery</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
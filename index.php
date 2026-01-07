<?php
session_start();
require 'koneksi.php';

// --- LOGIC SEARCH & PAGINATION ---
$limit = 8; 
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

try {
    $stmt = $pdo->prepare("SELECT * FROM sneakers WHERE nama_sneaker LIKE ? OR brand LIKE ? ORDER BY id DESC LIMIT $start, $limit");
    $stmt->execute(["%$cari%", "%$cari%"]);
    $sneakers = $stmt->fetchAll();

    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM sneakers WHERE nama_sneaker LIKE ? OR brand LIKE ?");
    $total_stmt->execute(["%$cari%", "%$cari%"]);
    $total_data = $total_stmt->fetchColumn();
    $total_pages = ceil($total_data / $limit);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNEAKER VAULT | By Sherly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --bg: #0b0c0e; 
            --card: #141619; 
            --accent: #3d8bff; 
            --text-muted: #8a8d91;
        }
        
        body { 
            background-color: var(--bg); 
            color: #fff; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
        }

        /* Navbar Styling */
        .navbar { 
            background: rgba(11, 12, 14, 0.9) !important; 
            backdrop-filter: blur(15px); 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            padding: 1rem 0;
        }
        .navbar-brand { font-weight: 800; letter-spacing: -1px; font-size: 1.5rem; }
        .navbar-brand span { color: var(--accent); }

        .search-container { position: relative; width: 300px; }
        .search-container i { 
            position: absolute; 
            left: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: rgba(255,255,255,0.5);
            z-index: 10;
        }
        .search-bar { 
            background: rgba(255,255,255,0.12) !important; 
            border: 1px solid rgba(255,255,255,0.2) !important; 
            color: #fff !important; 
            border-radius: 50px; 
            font-size: 0.85rem; 
            width: 100%; 
            padding: 10px 15px 10px 40px; 
            transition: 0.3s;
        }
        .search-bar::placeholder { color: rgba(255,255,255,0.4); }
        .search-bar:focus { 
            background: rgba(255,255,255,0.2) !important; 
            border-color: var(--accent) !important; 
            box-shadow: 0 0 15px rgba(61, 139, 255, 0.2); 
            outline: none;
        }

        /* Hero Section */
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), 
                        url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070'); 
            background-size: cover; 
            background-position: center; 
            padding: 120px 0; 
            text-align: center; 
            border-radius: 0 0 50px 50px; 
            margin-bottom: 50px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .hero h1 { font-size: 4rem; font-weight: 800; text-transform: uppercase; letter-spacing: -2px; }
        .hero p { color: var(--text-muted); font-size: 1.1rem; }

        /* Sneaker Cards */
        .card-sneaker { 
            background: var(--card); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 24px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            overflow: hidden; 
            height: 100%; 
            padding: 15px;
        }
        .card-sneaker:hover { 
            transform: translateY(-12px); 
            border-color: var(--accent); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .img-wrapper { 
            height: 200px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
            background: #fff; 
            border-radius: 18px;
            margin-bottom: 15px;
        }
        .img-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; }

        .brand-tag { font-size: 0.7rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; }
        .sneaker-name { font-size: 1rem; margin: 5px 0; font-weight: 700; color: #f8f9fa; }
        .price-tag { font-size: 1.2rem; font-weight: 800; color: #fff; }

        .btn-buy {
            background: var(--accent);
            border: none;
            border-radius: 12px;
            padding: 10px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-buy:hover { background: #2b74e6; transform: scale(1.02); }

        /* Pagination Styling Update */
        .pagination .page-link { 
            background: var(--card); 
            border: 1px solid rgba(255,255,255,0.05); 
            color: #fff; 
            margin: 0 4px; 
            border-radius: 12px !important; 
            padding: 10px 18px;
            transition: 0.3s;
        }
        .pagination .page-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--accent);
        }
        .pagination .page-item.active .page-link { 
            background: var(--accent); 
            border-color: var(--accent);
            box-shadow: 0 5px 15px rgba(61, 139, 255, 0.3);
        }
        .pagination .page-item.disabled .page-link {
            background: rgba(0,0,0,0.2);
            color: #444;
            border-color: transparent;
        }

        footer { 
            margin-top: auto; 
            background: #050505; 
            padding: 40px 0; 
            border-top: 1px solid rgba(255,255,255,0.05); 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index">SNEAKER<span>VAULT</span></a>
        
        <div class="d-flex align-items-center gap-3">
            <form action="index" method="GET" class="d-none d-md-flex">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" name="cari" class="form-control search-bar" placeholder="Cari kicks favoritmu..." value="<?= htmlspecialchars($cari) ?>">
                </div>
            </form>
            
            <?php if(isset($_SESSION['username'])): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-light rounded-pill px-4 dropdown-toggle border-0" style="background: rgba(255,255,255,0.05)" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-circle-user me-2 text-primary"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary mt-2">
                        <?php if($_SESSION['role'] == 'admin'): ?>
                            <li><a class="dropdown-item py-2" href="admin_dashboard"><i class="fas fa-gauge me-2"></i> Admin Panel</a></li>
                            <li><hr class="dropdown-divider opacity-10"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item py-2 text-danger" href="logout"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">SIGN IN</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="hero">
    <div class="container">
        <h1 class="animate__animated animate__fadeInDown">STEP INTO <span class="text-primary">STYLE</span></h1>
        <p class="animate__animated animate__fadeInUp">Premium & Authentic Sneaker Vault. Curated by <b>Sherly</b>.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php if (count($sneakers) > 0): ?>
            <?php foreach($sneakers as $s): ?>
            <div class="col">
                <div class="card card-sneaker">
                    <div class="img-wrapper">
                        <img src="<?= $s['gambar'] ?: 'https://via.placeholder.com/300x200?text=No+Image' ?>" alt="Sneaker">
                    </div>
                    <div class="card-body p-2 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="brand-tag"><?= htmlspecialchars($s['brand']) ?></span>
                            <span class="badge <?= $s['stok'] > 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' ?>" style="font-size: 0.65rem; border: 1px solid currentColor;">
                                <?= $s['stok'] > 0 ? 'STOCK: '.$s['stok'] : 'OUT OF STOCK' ?>
                            </span>
                        </div>
                        <h6 class="sneaker-name"><?= htmlspecialchars($s['nama_sneaker']) ?></h6>
                        <h5 class="price-tag mb-3">Rp<?= number_format($s['harga'], 0, ',', '.') ?></h5>
                        
                        <div class="mt-auto">
                            <?php if($s['stok'] > 0): ?>
                                <a href="checkout?id=<?= $s['id'] ?>" class="btn btn-primary btn-buy w-100">
                                    BUY NOW <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 rounded-pill fw-bold" disabled>SOLD OUT</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Oops! Sepatu yang lo cari nggak ketemu.</h4>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link shadow-lg" href="?halaman=<?= $page - 1 ?>&cari=<?= urlencode($cari) ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link shadow-lg" href="?halaman=<?= $i ?>&cari=<?= urlencode($cari) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link shadow-lg" href="?halaman=<?= $page + 1 ?>&cari=<?= urlencode($cari) ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<footer class="text-center">
    <div class="container text-secondary">
        <h5 class="text-white fw-800 mb-3">SNEAKER<span>VAULT</span></h5>
        <p class="small mb-2">Destinasi utama buat koleksi kicks original dan langka.</p>
        <div class="mb-4">
            <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-tiktok"></i></a>
        </div>
        <p class="mb-0 x-small">© 2026 Crafted with Passion by <b>Sherly Vault</b></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
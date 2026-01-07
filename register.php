<?php
require 'koneksi.php';

$error = ""; // Inisialisasi variabel error

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Kita paksa role-nya jadi 'user' di sini
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        $stmt->execute([$username, $password]);
        
        // Kalau berhasil, langsung lempar ke login dengan pesan sukses
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
        exit;
    } catch(PDOException $e) {
        // Cek jika errornya karena duplicate entry (username sama)
        if ($e->getCode() == 23000) {
            $error = "Waduh, username <strong>$username</strong> sudah ada yang punya!";
        } else {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Sneaker Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: #0f1012; 
            color: white; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0;
        }
        .register-card { 
            background: #1a1c1e; 
            padding: 40px; 
            border-radius: 24px; 
            width: 100%; 
            max-width: 400px; 
            border: 1px solid #333;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .form-control { 
            background: #222 !important; 
            color: white !important; 
            border: 1px solid #444 !important; 
            padding: 12px 15px;
            border-radius: 12px;
        }
        .form-control:focus {
            border-color: #0d6efd !important;
            box-shadow: none;
        }
        .btn-register {
            background: #0d6efd;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-register:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }
        label { font-size: 0.85rem; color: #888; margin-bottom: 5px; margin-left: 5px; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">SNEAKER VAULT</h2>
        <p class="text-secondary small">Daftar sekarang untuk mulai berburu sepatu!</p>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger border-0 small py-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label><i class="fas fa-user me-1"></i> Username</label>
            <input type="text" name="username" class="form-control" placeholder="Pilih username unik" required>
        </div>
        <div class="mb-4">
            <label><i class="fas fa-lock me-1"></i> Password</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password aman" required>
        </div>
        
        <button type="submit" name="register" class="btn btn-primary btn-register w-100 mb-3">
            DAFTAR SEKARANG
        </button>

        <p class="text-center small text-secondary">
            Sudah punya akun? <a href="login.php" class="text-primary text-decoration-none fw-bold">Login</a>
        </p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
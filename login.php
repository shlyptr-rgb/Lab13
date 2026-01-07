<?php
require 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Cek User Admin (Sherly)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user['username'] === 'sherly') {
        // Cek password untuk admin
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['role']     = 'admin';
            $_SESSION['username'] = 'Sherly';
            // REDIRECT TANPA .PHP
            header("Location: admin_dashboard");
            exit;
        } else {
            $error = "Kata sandi salah.";
        }
    } else {
        // 2. Akses Pengunjung (Bebas Masuk)
        $_SESSION['user_id']  = rand(1000, 9999);
        $_SESSION['role']     = 'user';
        $_SESSION['username'] = htmlspecialchars($username);
        // REDIRECT TANPA .PHP
        header("Location: index");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNEAKER VAULT | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                        url('https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=1974');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 35px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            color: white;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .brand-logo {
            font-weight: 800;
            letter-spacing: -2px;
            font-size: 2.5rem;
            margin-bottom: 5px;
            text-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .brand-logo span { color: #3d8bff; }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            border-radius: 15px;
            padding: 15px;
            transition: 0.3s;
        }

        .form-control::placeholder { color: rgba(255, 255, 255, 0.5); }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 0 20px rgba(61, 139, 255, 0.3);
            border-color: #3d8bff !important;
        }

        .btn-login {
            background: #3d8bff;
            border: none;
            padding: 16px;
            border-radius: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(61, 139, 255, 0.3);
        }

        .btn-login:hover {
            background: #2b74e6;
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(61, 139, 255, 0.4);
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.5px;
        }

        .footer-text b { color: #3d8bff; }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <h3 class="brand-logo">VAULT<span>SNEAKER</span></h3>
        <p class="text-white-50 small mb-5">Enter the vault to explore the collection.</p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white small mb-4 py-2">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login">
            <div class="mb-4 text-start">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Type your name..." required autocomplete="off">
            </div>
            <div class="mb-5 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password..." required>
            </div>
            <button type="submit" name="login" class="btn btn-login btn-primary w-100 mb-2">
                SIGN IN <i class="fas fa-arrow-right-to-bracket ms-2"></i>
            </button>
        </form>

        <div class="footer-text">
            SNEAKER VAULT &copy; 2026<br>
            Developed by <b>Sherly Vault</b>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
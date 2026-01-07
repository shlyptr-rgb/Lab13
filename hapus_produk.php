<?php
session_start();
require 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Ambil nama file gambar dulu biar bisa dihapus dari folder
    $stmt = $pdo->prepare("SELECT gambar FROM sneakers WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Hapus file fisik di folder img
        if (file_exists($data['gambar'])) {
            unlink($data['gambar']);
        }

        // 2. Hapus data dari database
        $delete = $pdo->prepare("DELETE FROM sneakers WHERE id = ?");
        $delete->execute([$id]);
    }

    header("Location: admin_dashboard.php");
}
?>
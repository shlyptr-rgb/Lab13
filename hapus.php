<?php
require 'koneksi.php';

// Cek apakah ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Perintah hapus data berdasarkan ID
        $stmt = $pdo->prepare("DELETE FROM sneakers WHERE id = ?");
        $stmt->execute([$id]);

        // Kalau berhasil, balik ke index.php dengan status sukses
        header("Location: index.php?pesan=hapus_berhasil");
        exit();
    } catch (PDOException $e) {
        die("Waduh, gagal hapus bro: " . $e->getMessage());
    }
} else {
    // Kalau nggak ada ID, balik aja ke home
    header("Location: index.php");
    exit();
}
<?php
// menghubungkan dengan koneksi
include 'koneksi.php';
session_start();

// menangkap data yang dikirim dari form
$nama_produk = $_POST['nama_produk'];
$jumlah = $_POST['jumlah'];
$alamat = $_POST['alamat'];

date_default_timezone_set("Asia/Jakarta");
// Mendapatkan tanggal dan waktu saat ini di zona waktu yang ditentukan
$tanggal_transaksi = date("Y-m-d H:i:s");

// menyeleksi data produk berdasarkan nama_produk
$produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk='$nama_produk'");

// Mengecek apakah produk ditemukan
if (mysqli_num_rows($produk) > 0) {
    $data_produk = mysqli_fetch_assoc($produk);

    // Cek stok produk
    if ($jumlah <= $data_produk['stock']) {
        // Jika stok cukup, lanjutkan dengan transaksi
        $nama_user = $_SESSION['nama_user'];
        $total_harga = $jumlah * $data_produk['harga_produk'];

        // Query untuk memasukkan transaksi ke database
        $query_transaksi = "INSERT INTO transaksi (nama_user, nama_produk, quantity, total_harga, tanggal_transaksi, alamat, status)
                            VALUES ('$nama_user', '$nama_produk', '$jumlah', '$total_harga', '$tanggal_transaksi', '$alamat', 'pending')";

        // Menjalankan query transaksi
        if (mysqli_query($koneksi, $query_transaksi)) {
            header("Location: profile_u.php?pesan=sukses");
            exit();
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        // Jika stok tidak mencukupi, redirect ke profile_u.php dengan pesan sold_out
        header("Location: profile_u.php?pesan=sold_out");
        exit();
    }
}

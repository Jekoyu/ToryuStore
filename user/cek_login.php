<?php
// mengaktifkan session php
session_start();

// menghubungkan dengan koneksi
include 'koneksi.php';

// menangkap data yang dikirim dari form
$email = $_POST['email'];
$password = $_POST['password'];

// Menyeleksi data user berdasarkan email
$data = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");

// Mengecek apakah data ditemukan
$cek = mysqli_num_rows($data);
if ($cek > 0) {
    // Mengambil data user
    $user = mysqli_fetch_assoc($data);

    // Memverifikasi password yang dimasukkan dengan hash yang ada di database
    if (password_verify($password, $user['password'])) {
        // Jika password cocok, simpan data ke session
        $_SESSION['nama_user'] = $user['nama_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['no_hp'] = $user['no_hp'];
        $_SESSION['status'] = "login";

        // Arahkan ke halaman profile_u.php
        header("location:profile_u.php");
    } else {
        // Jika password tidak cocok
        header("location:mineral.php?pesan=gagal");
    }
} else {
    // Jika email tidak ditemukan
    header("location:mineral.php?pesan=gagal");
}

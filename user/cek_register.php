<?php
// mengaktifkan session php
session_start();

// menghubungkan dengan koneksi
include 'koneksi.php';

// menangkap data yang dikirim dari form
$nama = $_POST['nama'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm-password'];
$no_hp = $_POST['no_hp'];

if ($password != $confirm_password) {
    header("location:mineral.php?pesan=password_not_match");
    exit();
} else {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
}
// Menyeleksi data user berdasarkan email
$data = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");

// Mengecek apakah user udah ada
$cek = mysqli_num_rows($data);
if ($cek > 0) {
    header("location:mineral.php?pesan=already_exist");
} else {

    $query_register = "INSERT INTO user (nama_user, email, password, no_hp)
                        VALUES ('$nama', '$email', '$password', '$no_hp')";

    // Menjalankan query registrasi
    if (mysqli_query($koneksi, $query_register)) {
        header("Location: mineral.php?pesan=sukses");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

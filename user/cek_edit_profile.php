<?php
session_start();
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$nama_user = $_POST['nama_user'];
$email = $_POST['email'];
$no_hp = $_POST['no_hp'];

// Query untuk memperbarui data pengguna di database
$query = "UPDATE user 
          SET nama_user = '$nama_user', email = '$email', no_hp = '$no_hp' 
          WHERE email = '" . $_SESSION['email'] . "'";

// Eksekusi query
if (mysqli_query($koneksi, $query)) {
    // Perbarui session dengan data baru
    $_SESSION['nama_user'] = $nama_user;
    $_SESSION['email'] = $email;
    $_SESSION['no_hp'] = $no_hp;

    // Redirect dengan pesan sukses
    header("Location: profile_u.php?pesan=update_success");
} else {
    // Redirect dengan pesan error jika query gagal
    header("Location: profile_u.php?pesan=update_error");
}
?>
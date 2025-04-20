<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-product {
            max-width: 18rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <button class="btn btn-secondary mb-4"><a class="text-white text-decoration-none" href="mineral.php">← Kembali</a></button>

        <!-- cek apakah sudah login -->
        <?php
        session_start();
        if ($_SESSION['status'] != "login") {
            header("location:mineral.php?pesan=belum_login");
        }

        // cek transaksi
        if (isset($_GET['pesan'])) {
            if ($_GET['pesan'] == "sold_out") {
                echo "<script>alert('Stock barang tidak mencukupi');</script>";
            } else if ($_GET['pesan'] == "sukses") {
                echo "<script>alert('Transaksi berhasil');</script>";
            }
        }
        ?>

        <h1 class="mb-4">Profile</h1>

        <!-- Informasi Akun -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Informasi Akun</h5>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Nama Pengguna</th>
                            <td><?php echo $_SESSION['nama_user']; ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo $_SESSION['email']; ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?php echo $_SESSION['no_hp']; ?></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    Ganti Password
                </button>
            </div>
        </div>

        <!-- Modal Edit Profil -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="cek_edit_profile.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="editName" name="nama_user" value="<?php echo $_SESSION['nama_user']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" value="<?php echo $_SESSION['email']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPhone" class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="editPhone" name="no_hp" value="<?php echo $_SESSION['no_hp']; ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- List Produk -->
        <h5 class="mb-4">Produk yang Tersedia</h5>
        <div class="row g-3 mb-4">

            <?php
            // Menghubungkan ke database
            include 'koneksi.php'; // Pastikan koneksi ke database sudah benar

            // Mengambil data produk dari database
            $query = "SELECT * FROM produk";
            $result = mysqli_query($koneksi, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id_produk = $row['id'];
                    $nama_produk = $row['nama_produk'];
                    $harga = $row['harga_produk'];
                    $gambar = $row['img']; // Pastikan ada kolom gambar di database
            ?>
                    <!-- Card Produk -->
                    <div class="col-md-4">
                        <div class="card card-product">
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['img']) ?>" class="card-img-top" alt="<?php echo $nama_produk; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $nama_produk; ?></h5>
                                <p class="card-text">Rp <?php echo number_format($harga, 0, ',', '.'); ?></p>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#buyProductModal" onclick="setProductName('<?php echo $nama_produk; ?>')">Beli</button>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>Tidak ada produk tersedia.</p>";
            }
            ?>

        </div>

        <!-- List Transaksi -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Riwayat Transaksi</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        // Menyeleksi transaksi berdasarkan nama user yang login
                        $query = "SELECT * FROM transaksi WHERE nama_user = '" . $_SESSION['nama_user'] . "'";
                        $result = mysqli_query($koneksi, $query);

                        if (mysqli_num_rows($result) > 0) {
                            $no = 1; // Inisialisasi nomor urut
                            // Mengambil data transaksi
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_transaksi = $row['id'];
                                $nama_produk = $row['nama_produk'];
                                $quantity = $row['quantity'];
                                $harga = $row['total_harga'];
                                $tanggal_transaksi = $row['tanggal_transaksi'];
                                $status = $row['status'];
                        ?>

                                <tr>
                                    <td><?php echo $no++; ?></td> <!-- Menampilkan nomor urut dan menambahkannya setelah setiap iterasi -->
                                    <td><?php echo $tanggal_transaksi; ?></td>
                                    <td>Pembelian <?php echo $nama_produk; ?></td>
                                    <td>Rp <?php echo number_format($harga, 0, ',', '.'); ?></td>
                                    <td>
                                        <!-- Menampilkan status transaksi -->
                                        <?php if ($status == 'success') { ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php } else if ($status == 'progres') { ?>
                                            <span class="badge bg-warning">Progres</span>
                                        <?php } else if ($status == 'pending') { ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        <?php } ?>
                                    </td>
                                </tr>

                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada transaksi ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Beli Produk -->
    <div class="modal fade" id="buyProductModal" tabindex="-1" aria-labelledby="buyProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buyProductModalLabel">Beli Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="buyProductForm" method="POST" action="transaksi.php">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="nama_produk" id="productName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jumlah" id="quantity" min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Pengiriman</label>
                            <textarea class="form-control" name="alamat" id="address" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Beli</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to set the product name in the modal
        function setProductName(name) {
            document.getElementById('productName').value = name;
        }
    </script>
</body>

</html>
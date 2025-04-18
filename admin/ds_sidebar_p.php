<?php
include 'koneksi.php';
include 'check_login.php';
// Proses Tambah Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_produk = $_POST['nama_produk'];
    $harga_produk = $_POST['harga_produk'];
    $stock = $_POST['stock'];

    $img = null;
    if (!empty($_FILES['img']['tmp_name'])) {
        $img = file_get_contents($_FILES['img']['tmp_name']);
    }

    $sql = "INSERT INTO produk (nama_produk, harga_produk, stock, img) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdis", $nama_produk, $harga_produk, $stock, $img);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Proses Edit Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $nama_produk = $_POST['nama_produk'];
    $harga_produk = $_POST['harga_produk'];
    $stock = $_POST['stock'];

    if (!empty($_FILES['img']['tmp_name'])) {
        $img = file_get_contents($_FILES['img']['tmp_name']);
        $sql = "UPDATE produk SET nama_produk = ?, harga_produk = ?, stock = ?, img = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdisi", $nama_produk, $harga_produk, $stock, $img, $id);
    } else {
        $sql = "SELECT img FROM produk WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $img = $row['img'];

        $sql = "UPDATE produk SET nama_produk = ?, harga_produk = ?, stock = ?, img = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdisi", $nama_produk, $harga_produk, $stock, $img, $id);
    }

    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Proses Hapus Produk
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM produk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil data produk dari database
$sql = "SELECT * FROM produk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling untuk Sidebar */
        #sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            z-index: 1000;
            overflow-y: auto;
        }

        #sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            margin: 10px 0;
        }

        #sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Card Styling */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card img {
            border-radius: 10px 10px 0 0;
            height: 150px;
            object-fit: cover;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .modal-dialog {
                max-width: 90%;
            }
            .card {
                width: 100%;
            }
        }

        /* Custom Header Styling */
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <h4 class="text-center text-white">Admin Dashboard</h4>
        <a href="ds_sidebar_p.php">Dashboard Produk</a>
        <a href="ds_sidebar_u.php">Dashboard User</a>
        <a href="ds_sidebar_t.php">Dashboard Transaksi</a>
        <a href="ds_sidebar_s.php">Dashboard Supplier</a>
        <a href="ds_sidebar_bm.php">Dashboard Barang Masuk</a>
        <a href="#" class="logout text-center text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="my-4">Dashboard Produk</h1>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Produk</button>

            <div class="row mt-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['img']) ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                            <p class="card-text">Harga: Rp <?= number_format($row['harga_produk'], 2, ',', '.') ?></p>
                            <p class="card-text">Stok: <?= htmlspecialchars($row['stock']) ?></p>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal" onclick='fillEditForm(<?= json_encode([
                                'id' => $row['id'],
                                'nama_produk' => $row['nama_produk'],
                                'harga_produk' => $row['harga_produk'],
                                'stock' => $row['stock']
                            ]) ?>'>Edit</button>
                            <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Produk -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga_produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="img" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="editNamaProduk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga_produk" id="editHargaProduk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stock" id="editStock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="img" id="editImg" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="window.location.href='login.php'">Logout</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillEditForm(product) {
            console.log(product);
            if (!product || !product.id) {
                console.error("Data produk tidak valid:", product);
                return;
            }
            document.getElementById('editId').value = product.id;
            document.getElementById('editNamaProduk').value = product.nama_produk;
            document.getElementById('editHargaProduk').value = product.harga_produk;
            document.getElementById('editStock').value = product.stock;
            document.getElementById('editImg').value = product.img;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
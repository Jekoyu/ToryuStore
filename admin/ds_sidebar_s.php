<?php

include 'koneksi.php';

// Tambah Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $name = $_POST['supplierName'];
    $phone = $_POST['supplierPhone'];
    $sql = "INSERT INTO supplier (nama_supplier, no_hp) VALUES ('$name', '$phone')";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Edit Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_supplier'])) {
    $id = $_POST['supplierId'];
    $name = $_POST['supplierName'];
    $phone = $_POST['supplierPhone'];
    $sql = "UPDATE supplier SET nama_supplier='$name', no_hp='$phone' WHERE id=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Hapus Supplier
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM supplier WHERE id=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil semua supplier
$suppliers = $conn->query("SELECT * FROM supplier");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
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

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .table th,
        .table td {
            text-align: center;
        }

        .pagination {
            justify-content: center;
        }

        .d-flex-between {
            display: flex;
            justify-content: space-between;
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
    <div class="container">
        <h1 class="my-4">Dashboard Supplier</h1>
        <div class="container mt-5">
            <div class="row mb-4 d-flex-between">
                <div class="col-md-3">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSupplierModal">Tambah Supplier</button>
                </div>
                <div class="col-md-3 offset-md-6">
                    <form id="searchForm" class="d-flex">
                        <input class="form-control" type="text" id="searchInput" placeholder="Cari transaksi..." oninput="searchTransactions()">
                    </form>
                </div>
            </div>

            <!-- Table for Suppliers -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $suppliers->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['nama_supplier'] ?></td>
                                <td><?= $row['no_hp'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="setEditModal(<?= $row['id'] ?>, '<?= $row['nama_supplier'] ?>', '<?= $row['no_hp'] ?>')" data-bs-toggle="modal" data-bs-target="#editSupplierModal">Edit</button>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Supplier -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="supplierName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                        </div>
                        <div class="mb-3">
                            <label for="supplierPhone" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="supplierPhone" name="supplierPhone" required>
                        </div>
                        <button type="submit" name="add_supplier" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Supplier -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="supplierId" name="supplierId">
                        <div class="mb-3">
                            <label for="editSupplierName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editSupplierName" name="supplierName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSupplierPhone" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="editSupplierPhone" name="supplierPhone" required>
                        </div>
                        <button type="submit" name="edit_supplier" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
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
        function setEditModal(id, name, phone) {
            document.getElementById('supplierId').value = id;
            document.getElementById('editSupplierName').value = name;
            document.getElementById('editSupplierPhone').value = phone;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
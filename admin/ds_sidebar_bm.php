<?php

include 'koneksi.php';
include 'check_login.php';
// CREATE / INSERT
if (isset($_POST['add'])) {
  $supplier = $_POST['supplier'];
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  $expense = $_POST['expense'];

  $product_query = "SELECT nama_produk FROM produk WHERE id = $product_id";
  $product_result = $conn->query($product_query);
  $product_row = $product_result->fetch_assoc();
  $product = $product_row['nama_produk']; // Nama produk diambil dari database

  // Masukkan data ke tabel barang_masuk
  $sql = "INSERT INTO barang_masuk (nama_supplier, nama_produk, quantity, pengeluaran) 
          VALUES ('$supplier', '$product', $quantity, $expense)";
  if ($conn->query($sql)) {
    // Update stok produk di tabel produk
    $updateStok = "UPDATE produk SET stock = stock + $quantity WHERE id = $product_id";
    $conn->query($updateStok);
  }
}

// UPDATE
if (isset($_POST['edit'])) {
  $id = $_POST['id'];
  $supplier = $_POST['supplier'];
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  $expense = $_POST['expense'];

  // Ambil nama produk berdasarkan product_id
  $product_query = "SELECT nama_produk FROM produk WHERE id = $product_id";
  $product_result = $conn->query($product_query);
  $product_row = $product_result->fetch_assoc();
  $product = $product_row['nama_produk']; // Nama produk diambil dari database

  // Update data di tabel barang_masuk
  $sql = "UPDATE barang_masuk SET 
            nama_supplier='$supplier', 
            nama_produk='$product', 
            quantity=$quantity, 
            pengeluaran=$expense 
          WHERE id=$id";
  $conn->query($sql);
}


// DELETE
if (isset($_POST['delete'])) {
  $id = $_POST['id'];
  $sql = "DELETE FROM barang_masuk WHERE id=$id";
  $conn->query($sql);
}

// FETCH DATA
$result = $conn->query("SELECT * FROM barang_masuk");

// FETCH SUPPLIER & PRODUK
$suppliers = $conn->query("SELECT * FROM supplier");
$products = $conn->query("SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Barang Masuk</title>
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
      z-index: 1000; /* Pastikan di bawah modal */
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

    .content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
      position: relative;
    }

    /* Perbaikan untuk modal */
    .modal-backdrop {
      z-index: 1040 !important; /* Lebih tinggi dari sidebar */
    }
    
    .modal {
      z-index: 1050 !important; /* Lebih tinggi dari backdrop */
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

    /* Responsiveness */
    @media (max-width: 768px) {
      .modal-dialog {
        max-width: 90%;
      }
      .card {
        width: 100%;
      }
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
  <div class="content">
    <h1 class="my-4">Dashboard Barang Masuk</h1>

    <!-- Button Tambah Barang Masuk -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Barang Masuk</button>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Supplier</th>
            <th>Nama Produk</th>
            <th>Quantity</th>
            <th>Pengeluaran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $result = $conn->query("SELECT * FROM barang_masuk");
          while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= $row['nama_supplier']; ?></td>
              <td><?= $row['nama_produk']; ?></td>
              <td><?= $row['quantity']; ?></td>
              <td><?= $row['pengeluaran']; ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">Edit</button>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $row['id']; ?>">
                  <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tambah Barang Masuk Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang Masuk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Nama Supplier</label>
              <select name="supplier" class="form-select" required>
                <option value="">Pilih Supplier</option>
                <?php 
                $suppliers = $conn->query("SELECT * FROM supplier");
                while ($sup = $suppliers->fetch_assoc()): ?>
                  <option value="<?= $sup['nama_supplier']; ?>"><?= $sup['nama_supplier']; ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Nama Produk</label>
              <select name="product_id" class="form-select" required>
                <option value="">Pilih Produk</option>
                <?php 
                $products = $conn->query("SELECT * FROM produk");
                while ($prod = $products->fetch_assoc()): ?>
                  <option value="<?= $prod['id']; ?>"><?= $prod['nama_produk']; ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Pengeluaran</label>
              <input type="number" name="expense" class="form-control" required>
            </div>

            <button type="submit" name="add" class="btn btn-success">Tambah</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal untuk setiap barang masuk -->
  <?php 
  $result = $conn->query("SELECT * FROM barang_masuk");
  while ($row = $result->fetch_assoc()): ?>
  <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Barang Masuk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id']; ?>">
            
            <div class="mb-3">
              <label class="form-label">Nama Supplier</label>
              <select name="supplier" class="form-select" required>
                <?php 
                $suppliers = $conn->query("SELECT * FROM supplier");
                while ($sup = $suppliers->fetch_assoc()): ?>
                  <option value="<?= $sup['nama_supplier']; ?>" <?= $sup['nama_supplier'] == $row['nama_supplier'] ? 'selected' : '' ?>>
                    <?= $sup['nama_supplier']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Nama Produk</label>
              <select name="product_id" class="form-select" required>
                <?php 
                $products = $conn->query("SELECT * FROM produk");
                while ($prod = $products->fetch_assoc()): ?>
                  <option value="<?= $prod['id']; ?>" <?= $prod['nama_produk'] == $row['nama_produk'] ? 'selected' : '' ?>>
                    <?= $prod['nama_produk']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" value="<?= $row['quantity']; ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Pengeluaran</label>
              <input type="number" name="expense" class="form-control" value="<?= $row['pengeluaran']; ?>" required>
            </div>

            <button type="submit" name="edit" class="btn btn-primary">Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; ?>

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

  <!-- Load JavaScript di bagian bawah body -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Inisialisasi modal
    $(document).ready(function() {
      $('#addModal').on('shown.bs.modal', function () {
        $('select[name="supplier"]').focus();
      });
    });
  </script>
</body>
</html>
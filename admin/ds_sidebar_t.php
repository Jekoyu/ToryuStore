<?php

include 'koneksi.php';
include 'check_login.php';

// Mengambil semua transaksi
function getAllTransactions()
{
  global $conn;
  $sql = "SELECT * FROM transaksi";
  $result = $conn->query($sql);
  return $result;
}

// Menambahkan transaksi baru
if (isset($_POST['add_transaction'])) {
  $nama_user = $_POST['nama_user'];
  $nama_produk = $_POST['nama_produk'];
  $quantity = $_POST['quantity'];
  $alamat = $_POST['alamat'];
  $tanggal_transaksi = $_POST['tanggal_transaksi'];
  $status = $_POST['status'];  // Menambahkan status

  // Ambil harga produk dari tabel produk
  $sql_produk = "SELECT harga_produk FROM produk WHERE nama_produk = '$nama_produk'";
  $result_produk = $conn->query($sql_produk);

  if ($result_produk->num_rows > 0) {
    // Ambil harga produk yang ada
    $row = $result_produk->fetch_assoc();
    $harga_produk = $row['harga_produk'];

    // Hitung total harga berdasarkan quantity dan harga produk
    $total_harga = $harga_produk * $quantity;

    // Menyimpan transaksi baru
    $sql = "INSERT INTO transaksi (nama_user, nama_produk, quantity, total_harga, tanggal_transaksi, alamat, status) 
              VALUES ('$nama_user', '$nama_produk', $quantity, $total_harga, '$tanggal_transaksi', '$alamat', '$status')";

    if ($conn->query($sql) === TRUE) {
      echo "New transaction added successfully";

      // Jika status transaksi adalah success, update stock produk
      if ($status == 'success') {
        // Ambil stock produk saat ini dari tabel produk
        $sql_stock = "SELECT stock FROM produk WHERE nama_produk = '$nama_produk'";
        $result_stock = $conn->query($sql_stock);

        if ($result_stock->num_rows > 0) {
          // Ambil stock produk yang ada
          $row_stock = $result_stock->fetch_assoc();
          $current_stock = $row_stock['stock'];

          // Tambahkan stock berdasarkan quantity transaksi
          $new_stock = $current_stock + $quantity;

          // Update stock produk di tabel produk
          $sql_update_stock = "UPDATE produk SET stock = $new_stock WHERE nama_produk = '$nama_produk'";

          if ($conn->query($sql_update_stock) === TRUE) {
            echo "Stock updated successfully.";
          } else {
            echo "Error updating stock: " . $conn->error;
          }
        } else {
          echo "Product not found!";
        }
      }
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } else {
    echo "Product not found!";
  }
}



// Mengedit transaksi
if (isset($_POST['edit_transaction'])) {
  $id = $_POST['id'];
  $nama_user = $_POST['nama_user'];
  $nama_produk = $_POST['nama_produk'];
  $quantity = $_POST['quantity'];
  $alamat = $_POST['alamat'];
  $tanggal_transaksi = $_POST['tanggal_transaksi'];
  $status = $_POST['status'];  // Menambahkan status

  // Ambil harga produk dari tabel produk
  $sql_produk = "SELECT harga_produk FROM produk WHERE nama_produk = '$nama_produk'";
  $result_produk = $conn->query($sql_produk);

  if ($result_produk->num_rows > 0) {
    // Ambil harga produk yang ada
    $row = $result_produk->fetch_assoc();
    $harga_produk = $row['harga_produk'];

    // Hitung total harga berdasarkan quantity dan harga produk
    $total_harga = $harga_produk * $quantity;

    // Update transaksi
    $sql = "UPDATE transaksi SET 
              nama_user='$nama_user', 
              nama_produk='$nama_produk', 
              quantity=$quantity, 
              total_harga=$total_harga,
              alamat='$alamat', 
              tanggal_transaksi='$tanggal_transaksi',
              status='$status'  
              WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
      echo "Transaction updated successfully";

      // Jika status transaksi adalah success, update stock produk
      if ($status == 'success') {
        // Ambil stock produk saat ini dari tabel produk
        $sql_stock = "SELECT stock FROM produk WHERE nama_produk = '$nama_produk'";
        $result_stock = $conn->query($sql_stock);

        if ($result_stock->num_rows > 0) {
          // Ambil stock produk yang ada
          $row_stock = $result_stock->fetch_assoc();
          $current_stock = $row_stock['stock'];

          // Tambahkan stock berdasarkan quantity transaksi
          $new_stock = $current_stock - $quantity;

          // Update stock produk di tabel produk
          $sql_update_stock = "UPDATE produk SET stock = $new_stock WHERE nama_produk = '$nama_produk'";

          if ($conn->query($sql_update_stock) === TRUE) {
            echo "Stock updated successfully.";
          } else {
            echo "Error updating stock: " . $conn->error;
          }
        } else {
          echo "Product not found!";
        }
      }
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } else {
    echo "Product not found!";
  }
}




// Menghapus transaksi
if (isset($_POST['delete'])) {
  $id = $_POST['id'];

  // SQL query untuk menghapus transaksi berdasarkan ID
  $sql = "DELETE FROM transaksi WHERE id = $id";

  if ($conn->query($sql) === TRUE) {
    echo "Transaction deleted successfully";
    // Redirect untuk menghindari refresh halaman dan menghindari pengulangan submit
    header("Location: ds_sidebar_t.php");
    exit();
  } else {
    echo "Error deleting record: " . $conn->error;
  }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard dengan Sidebar</title>
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

    .content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
    }

    /* Untuk memastikan container tidak melebihi lebar yang tersedia */
    .container {
      max-width: 100%;
      padding-right: 15px;
      padding-left: 15px;
      margin-right: auto;
      margin-left: auto;
    }

    /* Untuk tabel responsif */
    .table-responsive {
      overflow-x: auto;
    }

    /* Untuk modal yang tidak terpotong */
    .modal {
      z-index: 1050;
    }
  </style>
</head>

<body>

  <div id="sidebar">
    <h4 class="text-center text-white">Admin Dashboard</h4>
    <a href="ds_sidebar_p.php">Dashboard Produk</a>
    <a href="ds_sidebar_u.php">Dashboard User</a>
    <a href="ds_sidebar_t.php">Dashboard Transaksi</a>
    <a href="ds_sidebar_s.php">Dashboard Supplier</a>
    <a href="ds_sidebar_bm.php">Dashboard Barang Masuk</a>
    <a href="#" class="logout text-center text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
  </div>

  <div class="content">
    <h1 class="my-4">Dashboard Transaksi</h1>
    <div class="container mt-5">
      <div class="row mb-4 d-flex-between">
        <div class="col-md-3">
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahTransaksiModal">Tambah Transaksi</button>
        </div>
        <div class="col-md-3 offset-md-6">
          <form id="searchForm" class="d-flex">
            <input class="form-control" type="text" id="searchInput" placeholder="Cari transaksi..." oninput="searchTransactions()">
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>id</th>
              <th>Nama User</th>
              <th>Nama Produk</th>
              <th>Quantity</th>
              <th>Total Harga</th>
              <th>Tanggal Transaksi</th>
              <th>Alamat</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $transactions = getAllTransactions();
            while ($row = $transactions->fetch_assoc()):
            ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['nama_user']; ?></td>
                <td><?= $row['nama_produk']; ?></td>
                <td><?= $row['quantity']; ?></td>
                <td><?= $row['total_harga']; ?></td>
                <td><?= $row['tanggal_transaksi']; ?></td>
                <td><?= $row['alamat']; ?></td>
                <td><?= $row['status']; ?></td>
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTransactionModal"
                    onclick="editTransaction(<?= $row['id']; ?>, '<?= $row['nama_user']; ?>', '<?= $row['nama_produk']; ?>',
                          <?= $row['quantity']; ?>, <?= $row['total_harga']; ?>, '<?= $row['tanggal_transaksi']; ?>', '<?= $row['alamat']; ?>')">Edit</button>
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                  </form>

                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal untuk tambah transaksi -->
    <div class="modal fade" id="tambahTransaksiModal" tabindex="-1" aria-labelledby="tambahTransaksiModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahTransaksiModalLabel">Tambah Transaksi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="" method="POST">
              <div class="mb-3">
                <label for="nama_user" class="form-label">Nama User</label>
                <input type="text" class="form-control" id="nama_user" name="nama_user" required>
              </div>

              <!-- Dropdown untuk memilih produk -->
              <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <select class="form-select" id="nama_produk" name="nama_produk" required>
                  <option value="">Pilih Produk</option>
                  <?php
                  // Query untuk mendapatkan produk dari tabel produk
                  $sql_produk = "SELECT nama_produk FROM produk";
                  $result_produk = $conn->query($sql_produk);

                  if ($result_produk->num_rows > 0) {
                    while ($row = $result_produk->fetch_assoc()) {
                      echo "<option value='" . $row['nama_produk'] . "'>" . $row['nama_produk'] . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
              </div>

              <div class="mb-3">
                <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
              </div>

              <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" required>
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="pending">Pending</option>
                  <option value="progres">Progres</option>
                  <option value="success">Success</option>
                  <option value="failed">Failed</option>
                </select>
              </div>

              <button type="submit" name="add_transaction" class="btn btn-primary">Tambah Transaksi</button>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal untuk edit transaksi -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransaksiModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editTransaksiModalLabel">Edit Transaksi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="" method="POST">
              <input type="hidden" id="edit_id" name="id">

              <div class="mb-3">
                <label for="edit_nama_user" class="form-label">Nama User</label>
                <input type="text" class="form-control" id="edit_nama_user" name="nama_user" required>
              </div>

              <!-- Dropdown untuk memilih produk -->
              <div class="mb-3">
                <label for="edit_nama_produk" class="form-label">Nama Produk</label>
                <select class="form-select" id="edit_nama_produk" name="nama_produk" required>
                  <option value="">Pilih Produk</option>
                  <?php
                  // Query untuk mendapatkan produk dari tabel produk
                  $sql_produk = "SELECT nama_produk FROM produk";
                  $result_produk = $conn->query($sql_produk);

                  if ($result_produk->num_rows > 0) {
                    while ($row = $result_produk->fetch_assoc()) {
                      echo "<option value='" . $row['nama_produk'] . "'>" . $row['nama_produk'] . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="edit_quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
              </div>

              <div class="mb-3">
                <label for="edit_tanggal_transaksi" class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" id="edit_tanggal_transaksi" name="tanggal_transaksi" required>
              </div>
              <div class="mb-3">
                <label for="edit_alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
              </div>

              <div class="mb-3">
                <label for="edit_status" class="form-label">Status</label>
                <select class="form-select" id="edit_status" name="status" required>
                  <option value="pending">Pending</option>
                  <option value="progres">Progres</option>
                  <option value="success">Success</option>
                  <option value="failed">Failed</option>
                </select>
              </div>

              <button type="submit" name="edit_transaction" class="btn btn-primary">Edit Transaksi</button>
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

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function editTransaction(id, nama_user, nama_produk, quantity, total_harga, tanggal_transaksi, alamat, status) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nama_user').value = nama_user;
      document.getElementById('edit_nama_produk').value = nama_produk;
      document.getElementById('edit_quantity').value = quantity;
      document.getElementById('edit_total_harga').value = total_harga;
      document.getElementById('edit_tanggal_transaksi').value = tanggal_transaksi;
      document.getElementById('edit_alamat').value = alamat;
      document.getElementById('edit_status').value = status;
    }


    function searchTransactions() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const table = document.querySelector('table');
      const rows = table.querySelectorAll('tr');
      rows.forEach((row, index) => {
        if (index === 0) return; // Skip the header row
        const cells = row.querySelectorAll('td');
        let match = false;
        cells.forEach(cell => {
          if (cell.textContent.toUpperCase().includes(filter)) {
            match = true;
          }
        });
        row.style.display = match ? "" : "none";
      });
    }
  </script>
</body>

</html>
<?php
include 'koneksi.php';
include 'check_login.php';
// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addUser'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone = $_POST['phone'];

        $sql = "INSERT INTO user (nama_user, email, password, no_hp) VALUES ('$name', '$email', '$password', '$phone')";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>New user added successfully</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }

    if (isset($_POST['editUser'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $sql = "UPDATE user SET nama_user = '$name', email = '$email', no_hp = '$phone' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>User updated successfully</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }

    if (isset($_POST['deleteUser'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM user WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>User deleted successfully</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }
}

// Fetch all users excluding the 'status' column
$result = $conn->query("SELECT id, nama_user, email, no_hp FROM user");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
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

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .search-box {
            max-width: 300px;
        }

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
        <div class="container-fluid">
            <h1 class="my-4">Dashboard User</h1>
            
            <div class="d-flex justify-content-between mb-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle"></i> Tambah Pengguna
                </button>
                <div class="search-box">
                    <input class="form-control" type="text" id="searchInput" placeholder="Cari pengguna..." oninput="searchUsers()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php foreach ($users as $key => $user): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($user['nama_user']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['no_hp']) ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                    onclick="editUser(<?= $user['id'] ?>, '<?= addslashes($user['nama_user']) ?>', '<?= addslashes($user['email']) ?>', '<?= addslashes($user['no_hp']) ?>')">
                                    Edit
                                </button>
                                <form action="" method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="deleteUser" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="addUser" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="editUser" class="btn btn-primary">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
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
        function editUser(id, name, email, phone) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editPhone').value = phone;
        }

        function searchUsers() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const name = cells[1].textContent.toLowerCase();
                const email = cells[2].textContent.toLowerCase();
                const phone = cells[3].textContent.toLowerCase();
                
                if (name.includes(query) || email.includes(query) || phone.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'koneksi.php';

$query = "SELECT * FROM produk";
$result = mysqli_query($koneksi, $query);

// Inisialisasi variabel untuk grup produk
$products = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row; // Simpan produk dalam array
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toryuu Air Mineral</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" />

    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .navbar,
        .footer {
            background-color: #28a745;
            color: #fff;
        }

        .product-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .product-card img {
            border-radius: 8px 8px 0 0;
        }

        .btn-green {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .btn-green:hover {
            background-color: #218838;
        }

        .bg-hero {
            background-image: url("image.png");
            background-size: cover;
            background-position: center;
            height: 100vh;
            min-height: 600px;
        }

        .hero-content {
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
            margin-top: auto;
            margin-bottom: auto;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
        }

        .navbar .nav-link {
            color: #218838 !important;
            font-weight: 500;
        }

        .navbar .nav-link:hover {
            color: #28a745 !important;
        }

        .navbar .nav-item .nav-link {
            font-weight: bold;
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 25px;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            background-color: #28a745;
            color: white;
        }

        .modal-footer {
            text-align: center;
            color: #333;
        }

        .modal-footer a {
            color: #28a745;
            text-decoration: none;
        }

        .modal-footer a:hover {
            text-decoration: underline;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .form-label {
            font-weight: 500;
        }

        .modal-body .form-control {
            border-radius: 8px;
        }

        .modal-body .btn {
            border-radius: 8px;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        section {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        #home {
            background-color: #f8f9fa;
        }

        #services .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.25rem;
            }

            .navbar .nav-link {
                font-size: 1rem;
            }

            .product-card {
                margin-bottom: 20px;
            }

            .product-card img {
                max-height: 200px;
            }

            .bg-hero {
                height: 50vh;
            }

            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .product-card h5 {
                font-size: 1.1rem;
            }

            .product-card p {
                font-size: 0.9rem;
            }
        }

        #contact {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        #contact h2 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #333;
        }

        #contact p {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 30px;
        }

        #contact .list-unstyled {
            padding-left: 0;
            list-style-type: none;
        }

        #contact .list-unstyled li {
            font-size: 1.125rem;
            line-height: 1.75;
            color: #333;
            margin-bottom: 15px;
        }

        #contact .list-unstyled li a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }

        #contact .list-unstyled li a:hover {
            text-decoration: underline;
            color: #218838;
        }

        #contact .container {
            max-width: 960px;
        }

        @media (max-width: 768px) {
            #contact h2 {
                font-size: 2rem;
            }

            #contact p {
                font-size: 1.125rem;
            }

            #contact .list-unstyled li {
                font-size: 1rem;
            }
        }


        .navbar .nav-link {
            color: #218838 !important;
        }

        .navbar .nav-link:hover {
            color: inherit;
        }

        .navbar .nav-item .nav-link[data-bs-target="#loginModal"]:hover,
        .navbar .nav-item .nav-link[data-bs-target="#signupModal"]:hover {
            color: #fff !important;
            background-color: #28a745;
            border-radius: 5px;
        }

        .navbar .dropdown-toggle img {
            width: 30px;
            height: 30px;
            object-fit: cover;
        }
    </style>
    </style>
</head>

<body>
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="#">Toryuu Air Mineral </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    </li>
                    <!-- Dropdown Akun -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Akun</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="profile_u.php">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- cek pesan notifikasi -->
    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "gagal") {
            echo "<script>alert('Login gagal! email dan password salah!');</script>";
        } else if ($_GET['pesan'] == "logout") {
            echo "<script>alert('Anda telah berhasil logout');</script>";
        } else if ($_GET['pesan'] == "belum_login") {
            echo "<script>alert('Anda harus login terlebih dahulu');</script>";
        } else if ($_GET['pesan'] == "already_exist") {
            echo "<script>alert('Email sudah terdaftar');</script>";
        } else if ($_GET['pesan'] == "password_not_match") {
            echo "<script>alert('Password tidak konsisten');</script>";
        } else if ($_GET['pesan'] == "sukses") {
            echo "<script>alert('Registrasi Berhasil');</script>";
        }
    }
    ?>




    <!-- Hero Section -->
    <section class="text-center bg-hero d-flex align-items-center">

        <div class="container hero-content">
            <h1 class="fw-bold display-4">
                Segarkan Hidupmu dengan Air Mineral Berkualitas
            </h1>
            <p class="lead fs-4">
            Toryuu Air Mineral menghadirkan produk air mineral murni dan sehat untuk
                keluarga Anda.
            </p>
            <a href="#products" class="btn btn-light btn-lg px-4 py-2">Lihat Produk Kami</a>
        </div>
    </section>

    <!-- About Us -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold">Tentang Kami</h2>
            <p class="text-center text-muted mb-5">
            Toryuu Air Mineral berdedikasi untuk menyediakan air minum yang murni dan
                sehat kepada masyarakat. Kami menggunakan teknologi modern untuk
                memastikan kualitas terbaik dalam setiap tetes.
            </p>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold">Produk Kami</h2>
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php if (!empty($products)): ?>
                        <?php
                        $chunks = array_chunk($products, 3); // Bagi produk menjadi grup dengan 3 item
                        foreach ($chunks as $index => $chunk):
                        ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="row mt-4">
                                    <?php foreach ($chunk as $product): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="product-card">
                                                <img src="data:image/jpeg;base64,<?= base64_encode($product['img']) ?>" alt="<?= $product['nama_produk']; ?>" class="img-fluid" />
                                                <div class="p-3">
                                                    <h5 class="fw-bold"><?= $product['nama_produk']; ?></h5>
                                                    <p class="text-muted">Rp <?= number_format($product['harga_produk'], 0, ',', '.'); ?></p>
                                                    <a href="profile_u.php" class="btn btn-green btn-sm">Beli Sekarang</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">Tidak ada produk tersedia.</p>
                    <?php endif; ?>
                </div>

                <!-- Tombol Navigasi -->
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev" style="position: absolute; top: 50%; left: 0; transform: translateY(-50%);">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: black;"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: black;"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold">Hubungi Kami</h2>
            <p class="text-center text-muted mb-5">
                Berikut adalah cara untuk menghubungi kami:
            </p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><strong>Nomor HP:</strong> <a href="tel:+6281234567890">+62 812-3456-7890</a></li>
                        <li><strong>Email:</strong> <a href="mailto:info@airmineralco.com">info@airmineralco.com</a>
                        </li>
                        <li><strong>Alamat:</strong> Jl. Contoh Alamat No. 123, Jakarta, Indonesia</li>
                        <li><strong>Lokasi di Google Maps:</strong>
                            <a href="https://maps.app.goo.gl/cHjGyvXjdCbs15s76" target="_blank">Lihat di Google
                                Maps</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="cek_login.php" method="POST">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="loginEmail" placeholder="Enter your email"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="loginPassword"
                                placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <p class="mb-0">Don't have an account? <a href="#" data-bs-toggle="modal"
                            data-bs-target="#signupModal" data-bs-dismiss="modal">Sign Up</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="cek_register.php" method="POST">
                        <div class="mb-3">
                            <label for="signupName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="nama" id="signupName" placeholder="Enter your full name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="signupEmail" placeholder="Enter your email"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupNoHP" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="no_hp" id="signupNoHP" placeholder="Enter your Phone Number"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="signupPassword"
                                placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm-password" id="signupConfirmPassword"
                                placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Sign Up</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <p class="mb-0">Already have an account? <a href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer py-4 text-center">
        <p>&copy; 2024 Toryuu Air Mineral All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

</body>

</html>
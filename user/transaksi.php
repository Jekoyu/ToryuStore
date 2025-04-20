<?php
// menghubungkan dengan koneksi
include 'koneksi.php';
include '../midtrans_config.php';
session_start();

// menangkap data yang dikirim dari form
$nama_produk = $_POST['nama_produk'];
$jumlah = $_POST['jumlah'];
$alamat = $_POST['alamat'];

date_default_timezone_set("Asia/Jakarta");
// Mendapatkan tanggal dan waktu saat ini di zona waktu yang ditentukan
$tanggal_transaksi = date("Y-m-d H:i:s");
$id = "TRX-" . time();

// menyeleksi data produk berdasarkan nama_produk
$produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk='$nama_produk'");
$status = "pending"; // Status awal transaksi adalah pending
// Mengecek apakah produk ditemukan
if (mysqli_num_rows($produk) > 0) {
    $data_produk = mysqli_fetch_assoc($produk);

    // Cek stok produk
    if ($jumlah <= $data_produk['stock']) {
        // Jika stok cukup, lanjutkan dengan transaksi
        $nama_user = $_SESSION['nama_user'];
        $total_harga = $jumlah * $data_produk['harga_produk'];

        // Query untuk memasukkan transaksi ke database
      
        $params = [
            'transaction_details' => [
                'order_id' => $id,
                'gross_amount' => $total_harga,
            ],
            'item_details' => [[
                'id' => 'ITEM-' . rand(1000, 9999),
                'price' => $data_produk['harga_produk'],
                'quantity' => $jumlah,
                'name' => $nama_produk,
            ]],
            'customer_details' => [
                'first_name' => $nama_user,
                'shipping_address' => [
                    'first_name' => $nama_user,
                    'address' => $alamat,
                ]
            ]
        ];
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
      
            // --- Simpan ke database (status default dari input form) ---
            $sql_insert = "INSERT INTO transaksi (id, nama_user, nama_produk, quantity, total_harga, tanggal_transaksi, alamat, status)
                           VALUES ('$id', '$nama_user', '$nama_produk', $jumlah, $total_harga, '$tanggal_transaksi', '$alamat', '$status')";
      
            if ($koneksi->query($sql_insert) === TRUE) {
              // Jika status transaksi adalah success, update stok
              if ($status == 'success') {
                $sql_stock = "SELECT stock FROM produk WHERE nama_produk = '$nama_produk'";
                $result_stock = $koneksi->query($sql_stock);
      
                if ($result_stock->num_rows > 0) {
                  $row_stock = $result_stock->fetch_assoc();
                  $current_stock = $row_stock['stock'];
                  $new_stock = $current_stock + $quantity;
      
                  $sql_update_stock = "UPDATE produk SET stock = $new_stock WHERE nama_produk = '$nama_produk'";
                  $koneksi->query($sql_update_stock);
                }
              }
      
              // --- Tampilkan Midtrans Snap ---
              echo "
              <html>
              <head>
                <title>Proses Pembayaran</title>
                <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='SET_CLIENT_KEY_MU'></script>
              </head>
              <body>
                <script type='text/javascript'>
                  snap.pay('$snapToken', {
                    onSuccess: function(result){
              console.log(result);
      
              // Kirim order_id ke server untuk update status
              fetch('../midtrans_notification.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: result.order_id })
              })
              .then(res => res.json())
              .then(res => {
                console.log('Status update response:', res);
                // Redirect setelah update status
                window.location.href = 'profile_u.php?pesan=sukses';
              })
              .catch(err => {
                console.error('Status update error:', err);
                window.location.href = 'profile_u.php?pesan=sukses';
              });
            },
                    onPending: function(result){
                      alert('Menunggu pembayaran...');
                      console.log(result);
                      window.location.href = 'profile_u.php?pesan=sukses';
                    },
                    onError: function(result){
                      alert('Pembayaran gagal!');
                      console.log(result);
                      window.location.href = 'profile_u.php?pesan=sukses';
                    }
                  });
                </script>
              </body>
              </html>";
              exit;
            } else {
              echo "Gagal menyimpan transaksi: " . $koneksi->error;
            }
          } catch (Exception $e) {
            echo "Terjadi kesalahan dengan Midtrans: " . $e->getMessage();
          }
        // Menjalankan query transaksi
        if (mysqli_query($koneksi, $query_transaksi)) {
            header("Location: profile_u.php?pesan=sukses");
            exit();
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        // Jika stok tidak mencukupi, redirect ke profile_u.php dengan pesan sold_out
        header("Location: profile_u.php?pesan=sold_out");
        exit();
    }
}

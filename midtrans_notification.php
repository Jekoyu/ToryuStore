<?php
// update_status.php

require 'admin/koneksi.php'; // atau file koneksi sesuai project kamu

// Terima data dari fetch()
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id'])) {
    $order_id = $data['order_id'];

    // Update status jadi success
    $sql = "UPDATE transaksi SET status = 'success' WHERE id = '$order_id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Status updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>

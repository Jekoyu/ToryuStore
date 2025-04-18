<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "167.99.79.36";
$username = "jekoyu";
$password = "@Jekoyu157";
$dbname = "toryu";

// Koneksi Database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

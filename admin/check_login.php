<?php
session_start();

// Kalau belum login, redirect ke login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit;
}

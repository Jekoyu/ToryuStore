<?php
require_once dirname(__FILE__) . '/midtrans-php/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-K71k9GYTf7x4DssG9MeJGlTJ';
\Midtrans\Config::$isProduction = false; // Ganti true kalau sudah live
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
?>

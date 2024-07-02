<?php
session_start();

$totalItems = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

echo json_encode(['total' => $totalItems]);
?>

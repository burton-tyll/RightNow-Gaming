<?php
session_start();

header('Content-Type: application/json');

// Vérifiez si le panier est défini
if (isset($_SESSION['cart'])) {
    $total = array_sum(array_map(function($data) {
        return isset($data['platforms']) ? array_sum($data['platforms']) : 0;
    }, $_SESSION['cart']));
    echo json_encode(['total' => $total]);
} else {
    echo json_encode(['total' => 0]);
}
?>

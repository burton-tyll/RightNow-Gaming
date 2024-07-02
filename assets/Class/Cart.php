<?php
session_start();

class Cart {

    public function getCartItems() {
        if (isset($_SESSION['cart'])) {
            return $_SESSION['cart'];
        }
        return [];
    }

    public function getTotalItems() {
        if (isset($_SESSION['cart'])) {
            return array_sum(array_map(function($data) {
                return isset($data['platforms']) ? array_sum($data['platforms']) : 0;
            }, $_SESSION['cart']));
        }
        return 0;
    }
}

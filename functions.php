<?php
require_once 'config.php';
session_start();

function get_products($search = '', $sort = '', $category = '') {
    global $pdo;
    $sql = "SELECT * FROM products WHERE 1";
    $params = [];
    if ($search) {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%";
    }
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    if ($sort == 'price_asc') {
        $sql .= " ORDER BY price ASC";
    } elseif ($sort == 'price_desc') {
        $sql .= " ORDER BY price DESC";
    } elseif ($sort == 'name_asc') {
        $sql .= " ORDER BY name ASC";
    } elseif ($sort == 'name_desc') {
        $sql .= " ORDER BY name DESC";
    } else {
        $sql .= " ORDER BY id DESC";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_product($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function get_cart_items() {
    global $pdo;
    $session_id = session_id();
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
    $stmt->execute([$session_id]);
    return $stmt->fetchAll();
}

function cart_total() {
    $items = get_cart_items();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function get_cart_count() {
    global $pdo;
    $session_id = session_id();
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

function clear_cart() {
    global $pdo;
    $session_id = session_id();
    $pdo->prepare("DELETE FROM cart_items WHERE session_id = ?")->execute([$session_id]);
}

function get_voucher($code) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND valid_from <= CURDATE() AND valid_to >= CURDATE() AND used_count < usage_limit");
    $stmt->execute([$code]);
    return $stmt->fetch();
} 
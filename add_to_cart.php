<?php
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $session_id = session_id();
    
    try {
        // Log the values we're trying to add
        error_log("Adding to cart - Product ID: " . $product_id . ", Quantity: " . $quantity . ", Session ID: " . $session_id);
        
        // Check if product exists
        $check = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $check->execute([$product_id]);
        if (!$check->fetch()) {
            throw new Exception("Product not found");
        }

        // Check if product exists in cart
        $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE product_id = ? AND session_id = ?");
        $stmt->execute([$product_id, $session_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE product_id = ? AND session_id = ?");
            $result = $stmt->execute([$quantity, $product_id, $session_id]);
            if (!$result) {
                throw new Exception("Failed to update cart quantity");
            }
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)");
            $result = $stmt->execute([$session_id, $product_id, $quantity]);
            if (!$result) {
                throw new Exception("Failed to add item to cart");
            }
        }

        // Get updated cart count
        $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $cart_count = $stmt->fetch()['count'] ?? 0;

        // Get updated total
        $total = cart_total();

        echo json_encode([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cart_count,
            'total' => $total
        ]);
    } catch (Exception $e) {
        error_log("Cart add error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 
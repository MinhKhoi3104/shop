<?php
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $session_id = session_id();
    
    try {
        // Log the values we're trying to update
        error_log("Updating cart - Product ID: " . $product_id . ", Quantity: " . $quantity . ", Session ID: " . $session_id);
        
        // First check if the item exists
        $check = $pdo->prepare("SELECT * FROM cart_items WHERE product_id = ? AND session_id = ?");
        $check->execute([$product_id, $session_id]);
        $item = $check->fetch();
        
        if (!$item) {
            throw new Exception("Item not found in cart");
        }

        // Update the quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE product_id = ? AND session_id = ?");
        $result = $stmt->execute([$quantity, $product_id, $session_id]);
        
        if (!$result) {
            throw new Exception("Failed to update quantity");
        }

        // Get updated total
        $total = cart_total();

        echo json_encode([
            'success' => true,
            'message' => 'Đã cập nhật số lượng',
            'total' => $total
        ]);
    } catch (Exception $e) {
        error_log("Cart update error: " . $e->getMessage());
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
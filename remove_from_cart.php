<?php
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $session_id = session_id();
    
    try {
        // Delete the item
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE product_id = ? AND session_id = ?");
        $result = $stmt->execute([$product_id, $session_id]);
        
        if (!$result) {
            throw new Exception("Failed to remove item from cart");
        }

        // Get updated total
        $total = cart_total();

        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'total' => $total
        ]);
    } catch (Exception $e) {
        error_log("Cart remove error: " . $e->getMessage());
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
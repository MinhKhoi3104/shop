<?php
require_once 'functions.php';
require_once 'config.php';

// Set proper content type for JSON response
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'address', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Vui lòng điền đầy đủ thông tin " . $field);
        }
    }

    // Get cart items
    $cart_items = get_cart_items();
    if (empty($cart_items)) {
        throw new Exception("Giỏ hàng trống");
    }

    // Calculate total
    $total = cart_total();

    // Start transaction
    global $pdo;
    $pdo->beginTransaction();

    try {
        // Get session ID
        $session_id = session_id();

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (session_id, customer_name, email, phone, address, payment_method, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $session_id,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['payment_method'],
            $total
        ]);
        
        $order_id = $pdo->lastInsertId();

        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($cart_items as $item) {
            // Verify product exists
            $product = get_product($item['product_id']);
            if (!$product) {
                throw new Exception("Sản phẩm không tồn tại: " . $item['name']);
            }

            $stmt->execute([
                $order_id,
                $item['product_id'], // Use product_id from cart_items
                $item['quantity'],
                $item['price']
            ]);
        }

        // Clear cart
        clear_cart();

        // Commit transaction
        $pdo->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Đơn hàng đã được tạo thành công',
            'order_id' => $order_id
        ]);
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
} 
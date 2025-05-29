<?php
require_once 'functions.php';
include 'templates/header.php';

/**
 * Order Success Page
 * 
 * This page handles the successful completion of an order.
 * It is called by payment gateways after successful payment.
 * 
 * Process:
 * 1. Verify payment status
 * 2. Clear the cart after successful order
 * 3. Display success message to user
 * 4. Provide option to continue shopping
 */

// Check if this is a valid payment completion
$paymentStatus = $_GET['status'] ?? '';
$paymentSource = $_GET['source'] ?? '';

// If no valid payment status or source, redirect to home
if (empty($paymentStatus) || empty($paymentSource)) {
    header('Location: index.php');
    exit;
}

// Only clear cart if payment was successful
if ($paymentStatus === 'success') {
    // Clear cart after successful order
    $_SESSION['cart'] = [];
} else {
    // If payment was not successful, redirect to checkout
    header('Location: checkout.php?error=' . $paymentSource . '&message=' . urlencode(json_encode(['message' => 'Payment was not completed'])));
    exit;
}
?>

<!-- Order Success Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center py-5">
            <!-- Success icon -->
            <i class="fas fa-check-circle fa-5x text-primary mb-4"></i>
            
            <!-- Success message -->
            <h1 class="display-5 mb-4">Đặt hàng thành công!</h1>
            <p class="text-muted mb-4">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
            
            <!-- Continue shopping button -->
            <a href="/shop" class="btn btn-primary border-2 border-secondary py-3 px-4 rounded-pill text-white">
                <i class="fa fa-shopping-bag me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    </div>
</div>
<!-- Order Success End -->

<?php include 'templates/footer.php'; ?> 
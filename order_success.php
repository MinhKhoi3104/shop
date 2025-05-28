<?php
require_once 'functions.php';
include 'templates/header.php';

/**
 * Order Success Page
 * 
 * This page handles the successful completion of an order.
 * It is called by MoMo payment gateway after successful payment.
 * 
 * Process:
 * 1. Clear the cart after successful order
 * 2. Display success message to user
 * 3. Provide option to continue shopping
 */

// Clear cart after successful order
$_SESSION['cart'] = [];
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
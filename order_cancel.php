<?php
require_once 'functions.php';
include 'templates/header.php';

// Get payment source and message
$paymentSource = $_GET['source'] ?? '';
$message = $_GET['message'] ?? '';

// Get specific message based on payment source
$displayMessage = '';
if ($paymentSource === 'momo') {
    $displayMessage = $message ?: 'Bạn đã hủy thanh toán MoMo. Đơn hàng của bạn chưa được xử lý.';
} else if ($paymentSource === 'paypal') {
    $displayMessage = 'Bạn đã hủy thanh toán PayPal. Đơn hàng của bạn chưa được xử lý.';
} else {
    $displayMessage = 'Bạn đã hủy thanh toán. Đơn hàng của bạn chưa được xử lý.';
}
?>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-light rounded p-5 text-center">
                    <i class="fas fa-times-circle text-danger fa-4x mb-4"></i>
                    <h2 class="mb-4">Thanh toán đã bị hủy</h2>
                    <p class="mb-4"><?php echo htmlspecialchars($displayMessage); ?></p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                        <a href="checkout.php" class="btn btn-outline-primary">Quay lại thanh toán</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?> 
<?php
require_once 'functions.php';
include 'templates/header.php';

/**
 * Checkout Page
 * 
 * This page handles the checkout process and payment integration.
 * It includes:
 * 1. Cart validation
 * 2. Customer information collection
 * 3. Payment method selection
 * 4. MoMo payment integration
 * 5. Order processing
 */

// Get cart items and validate
$cart_items = get_cart_items();
if (empty($cart_items)) {
    header('Location: index.php');
    exit;
}

// Calculate total amount
$total = cart_total();

// Check for MoMo payment errors
$error = $_GET['error'] ?? '';
$error_message = $_GET['message'] ?? '';
?>

<!-- Checkout Start -->
<div class="container-fluid py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="mx-auto text-center mb-5" style="max-width: 500px;">
            <h5 class="text-primary text-uppercase">Thanh toán</h5>
            <h1 class="display-5">Hoàn tất đơn hàng</h1>
        </div>

        <!-- MoMo Payment Error Alert -->
        <?php if ($error === 'momo'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Lỗi thanh toán MoMo!</strong> 
            <?php 
            if ($error_message) {
                $message = json_decode(urldecode($error_message), true);
                echo isset($message['message']) ? $message['message'] : 'Có lỗi xảy ra khi xử lý thanh toán.';
            } else {
                echo 'Có lỗi xảy ra khi xử lý thanh toán.';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- Customer Information Form -->
            <div class="col-lg-8">
                <form id="checkoutForm" class="bg-light rounded p-4">
                    <div class="row g-3">
                        <!-- Required Fields -->
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ *</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <!-- Optional Fields -->
                        <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>
                        <!-- Terms and Conditions -->
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">Tôi đồng ý với các điều khoản và điều kiện *</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary and Payment -->
            <div class="col-lg-4">
                <div class="bg-light rounded p-4">
                    <!-- Order Items Table -->
                    <h4 class="mb-4">Đơn hàng của bạn</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Tổng cộng:</th>
                                    <th class="text-primary"><?= number_format($total, 0, ',', '.') ?>đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mt-4">
                        <h5 class="mb-3">Phương thức thanh toán</h5>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="payment_method" id="bank" value="bank">
                            <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="payment_method" id="momo" value="momo">
                            <label class="form-check-label" for="momo">Ví MoMo</label>
                            <small class="text-muted d-block ms-4">(Áp dụng cho đơn hàng từ 50.000đ)</small>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="payment_method" id="paypal" value="paypal">
                            <label class="form-check-label" for="paypal">
                                <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" style="height: 20px;">
                            </label>
                        </div>
                    </div>

                    <!-- Payment Notes -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Thanh toán qua MoMo chỉ áp dụng cho đơn hàng từ 50.000đ</li>
                            <li>Thanh toán qua PayPal sẽ được chuyển đổi sang USD theo tỷ giá hiện tại</li>
                            <li>Vui lòng kiểm tra kỹ thông tin đơn hàng trước khi thanh toán</li>
                            <li>Nếu có thắc mắc, vui lòng liên hệ hotline: 1900 1900</li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" onclick="processPayment()" class="btn btn-primary border-2 border-secondary py-3 px-4 rounded-pill text-white w-100 mt-4">
                        <i class="fa fa-credit-card me-2"></i>Đặt hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Checkout End -->

<script>
/**
 * Payment Processing Function
 * 
 * Handles the payment process based on selected payment method:
 * 1. Validates form data
 * 2. Checks MoMo minimum amount requirement
 * 3. Processes payment through selected method
 * 4. Handles MoMo payment redirection
 * 5. Processes PayPal payment
 * 6. Processes regular orders
 */
function processPayment() {
    const form = document.getElementById('checkoutForm');
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const total = <?= $total ?>;
    
    // Validate form data
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Create form data
    const formData = new FormData(form);
    formData.append('payment_method', paymentMethod);

    // Handle different payment methods
    switch (paymentMethod) {
        case 'cod':
            // Process COD order
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'order_success.php?status=success&source=cod';
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi xử lý đơn hàng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xử lý đơn hàng');
            });
            break;

        case 'momo':
            // Check minimum amount requirement
            if (total < 50000) {
                alert('Thanh toán qua MoMo chỉ áp dụng cho đơn hàng từ 50.000đ. Vui lòng chọn phương thức thanh toán khác.');
                return;
            }

            // Process MoMo payment
            fetch('momo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.message || 'Payment processing failed');
                }
                if (data.payUrl) {
                    window.location.href = data.payUrl;
                } else {
                    throw new Error('No payment URL received');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xử lý thanh toán MoMo: ' + error.message);
            });
            break;

        case 'paypal':
            // Process PayPal payment
            fetch('add_paypal_gateway.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.message || 'Payment processing failed');
                }
                if (data.paypalUrl) {
                    window.location.href = data.paypalUrl;
                } else {
                    throw new Error('No payment URL received');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xử lý thanh toán PayPal: ' + error.message);
            });
            break;

        default:
            alert('Phương thức thanh toán không hợp lệ');
            break;
    }
}

// Check for payment cancellation
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
const source = urlParams.get('source');

if (status === 'cancelled' && (source === 'momo' || source === 'paypal')) {
    window.location.href = 'order_cancel.php?source=' + source;
}
</script>

<?php include 'templates/footer.php'; ?> 
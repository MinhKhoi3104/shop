<?php
require_once 'functions.php';
include 'templates/header.php';

$search = $_GET['search'] ?? '';
$products = get_products($search);
$cart_items = get_cart_items();
$total = cart_total();
?>

<!-- Products Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="mx-auto text-center mb-5" style="max-width: 500px;">
            <h5 class="text-primary text-uppercase">Sản phẩm của chúng tôi</h5>
            <h1 class="display-5">Trái cây tươi ngon mỗi ngày</h1>
        </div>
        <div class="row g-4">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center"><p>Không có sản phẩm nào.</p></div>
            <?php else: foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <div class="position-relative bg-light rounded">
                            <img class="img-fluid w-100 rounded" src="/shop/assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-3"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="mb-3"><?= htmlspecialchars($product['description']) ?></p>
                            <div class="mb-3">
                                <span class="text-primary fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                            </div>
                            <form action="add_to_cart.php" method="post" class="d-flex justify-content-center add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-primary border-2 border-secondary py-2 px-4 rounded-pill text-white">
                                    <i class="fa fa-shopping-bag me-2"></i>Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
<!-- Products End -->

<!-- Cart Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="mx-auto text-center mb-5" style="max-width: 500px;">
            <h5 class="text-primary text-uppercase">Giỏ hàng</h5>
            <h1 class="display-5">Sản phẩm của bạn</h1>
        </div>
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if (empty($cart_items)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h3>Giỏ hàng trống</h3>
                        <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng của bạn</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Xóa</th>
                                    <th style="width: 100px;">Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th style="width: 150px;">Giá</th>
                                    <th style="width: 150px;">Số lượng</th>
                                    <th style="width: 150px;">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                ?>
                                <tr>
                                    <td>
                                        <form action="remove_from_cart.php" method="post" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <img src="/shop/assets/images/<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 80px;">
                                    </td>
                                    <td>
                                        <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>đ</span>
                                    </td>
                                    <td>
                                        <form action="update_cart.php" method="post" class="d-flex align-items-center update-quantity-form">
                                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                            <input type="number" name="quantity" class="form-control form-control-sm text-center" value="<?= $item['quantity'] ?>" min="1" style="width: 70px;">
                                        </form>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="bg-light rounded p-4">
                    <h4 class="mb-4">Tổng đơn hàng</h4>
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="mb-0">Tổng tiền:</h5>
                        <h5 class="mb-0 text-primary"><?= number_format($total, 0, ',', '.') ?>đ</h5>
                    </div>
                    <?php if (!empty($cart_items)): ?>
                        <a href="/shop/checkout.php" class="btn btn-primary border-2 border-secondary py-3 px-4 rounded-pill text-white w-100">
                            <i class="fa fa-credit-card me-2"></i>Thanh toán
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Cart End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle add to cart
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            const productId = formData.get('product_id');
            
            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Đang xử lý...';
            
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count if you have one
                    if (data.cart_count) {
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }

                    // Check if item exists in cart table and update its quantity
                    const cartItem = document.querySelector(`.update-quantity-form input[name="product_id"][value="${productId}"]`);
                    if (cartItem) {
                        const quantityInput = cartItem.closest('form').querySelector('input[name="quantity"]');
                        const currentQuantity = parseInt(quantityInput.value);
                        const newQuantity = currentQuantity + parseInt(formData.get('quantity') || 1);
                        quantityInput.value = newQuantity;

                        // Update the subtotal for this item
                        const row = quantityInput.closest('tr');
                        const price = parseFloat(row.querySelector('td:nth-child(4) .text-primary').textContent.replace(/[^\d]/g, ''));
                        const subtotal = price * newQuantity;
                        row.querySelector('td:last-child .text-primary').textContent = 
                            subtotal.toLocaleString('vi-VN') + 'đ';
                    } else {
                        // If item doesn't exist in cart, reload the page to show new item
                        window.location.reload();
                    }

                    // Update total if provided
                    if (data.total) {
                        const totalElement = document.querySelector('.col-lg-4 .d-flex.justify-content-between .text-primary');
                        if (totalElement) {
                            totalElement.textContent = data.total.toLocaleString('vi-VN') + 'đ';
                        }
                    }

                    // Show success message
                    submitButton.innerHTML = '<i class="fa fa-check me-2"></i>Đã thêm';
                    setTimeout(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }, 1000);
                } else {
                    // Only show error if there was an actual error
                    if (data.message) {
                        alert(data.message);
                    }
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    });

    // Handle quantity updates
    document.querySelectorAll('.update-quantity-form input[name="quantity"]').forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            const formData = new FormData(form);
            const originalValue = this.value; // Store original value in case of error
            
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the subtotal for this item
                    const row = this.closest('tr');
                    const price = parseFloat(row.querySelector('td:nth-child(4) .text-primary').textContent.replace(/[^\d]/g, ''));
                    const quantity = parseInt(this.value);
                    const subtotal = price * quantity;
                    row.querySelector('td:last-child .text-primary').textContent = 
                        subtotal.toLocaleString('vi-VN') + 'đ';
                    
                    // Update total
                    if (data.total) {
                        const totalElement = document.querySelector('.col-lg-4 .d-flex.justify-content-between .text-primary');
                        if (totalElement) {
                            totalElement.textContent = data.total.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                } else {
                    // Only show error and reset if there was an actual error
                    if (data.message) {
                        alert(data.message);
                        this.value = originalValue;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.value = originalValue;
            });
        });
    });

    // Handle remove from cart
    document.querySelectorAll('form[action="remove_from_cart.php"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const row = this.closest('tr');
            
            fetch('remove_from_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row with animation
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // If no items left, reload the page to show empty cart
                        const tbody = document.querySelector('table tbody');
                        if (!tbody.children.length) {
                            window.location.reload();
                            return;
                        }
                        
                        // Update total for non-empty cart
                        if (data.total) {
                            const totalElement = document.querySelector('.col-lg-4 .d-flex.justify-content-between .text-primary');
                            if (totalElement) {
                                totalElement.textContent = data.total.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }, 300);
                } else {
                    // Only show error if there was an actual error
                    if (data.message) {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?> 
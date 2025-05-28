<?php
require_once 'functions.php';
include 'templates/header.php';

$id = $_GET['id'] ?? 0;
$product = get_product($id);

if (!$product) {
    header('Location: index.php');
    exit;
}
?>

<!-- Product Detail Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="position-relative bg-light rounded">
                    <img class="img-fluid w-100 rounded" src="/shop/assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <h1 class="mb-4"><?= htmlspecialchars($product['name']) ?></h1>
                <div class="mb-4">
                    <span class="text-primary fw-bold fs-3"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                </div>
                <p class="mb-4"><?= htmlspecialchars($product['description']) ?></p>
                <form action="add_to_cart.php" method="post" class="d-flex align-items-center mb-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="input-group me-3" style="width: 100px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity()">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" name="quantity" id="quantity" class="form-control form-control-sm text-center" value="1" min="1">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 rounded-pill text-white">
                        <i class="fa fa-shopping-bag me-2"></i>Thêm vào giỏ
                    </button>
                </form>
                <div class="d-flex align-items-center mb-4">
                    <div class="d-flex align-items-center me-4">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        <span>Hàng chính hãng</span>
                    </div>
                    <div class="d-flex align-items-center me-4">
                        <i class="fas fa-truck text-primary me-2"></i>
                        <span>Miễn phí vận chuyển</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-undo text-primary me-2"></i>
                        <span>Đổi trả trong 7 ngày</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">Chia sẻ:</span>
                    <a class="btn btn-square btn-primary rounded-circle me-2" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-square btn-primary rounded-circle me-2" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-square btn-primary rounded-circle me-2" href=""><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-square btn-primary rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Detail End -->

<?php include 'templates/footer.php'; ?>

<script>
function decreaseQuantity() {
    var input = document.getElementById('quantity');
    var value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function increaseQuantity() {
    var input = document.getElementById('quantity');
    var value = parseInt(input.value);
    input.value = value + 1;
}
</script> 
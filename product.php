<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = db_one(
    "SELECT c.*, u.full_name AS seller_name
     FROM tblClothes c
     LEFT JOIN tblUser u ON u.user_id = c.seller_id
     WHERE c.clothes_id = ?",
    'i',
    [$productId]
);

if (!$product) {
    set_flash('Product not found.', 'error');
    redirect_to('shop.php');
}

if (is_post()) {
    Cart::AddItem($productId);
    set_flash('Item added to cart.', 'success');
    redirect_to('cart.php');
}

$galleryImages = [];
try {
    $productImageRows = db_all('SELECT image_path FROM tblProductImage WHERE clothes_id = ? ORDER BY sort_order ASC, image_id ASC', 'i', [$productId]);
    foreach ($productImageRows as $row) {
        $galleryImages[] = (string) $row['image_path'];
    }
} catch (Throwable $exception) {
    $galleryImages = [];
}

if ($galleryImages === []) {
    $galleryImages[] = product_image_path($product);
}

$mainImage = $galleryImages[0];
$galleryImages = array_values(array_unique(array_merge($galleryImages, [
    'assets/images/products/floral-dress.jpg',
    'assets/images/products/denim-jacket.jpg',
    'assets/images/products/silk-blouse.jpg',
    'assets/images/products/wool-coat.jpg',
])));
$galleryImages = array_slice($galleryImages, 0, 4);

$pageTitle = 'Pastimes | Product';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container product-layout">
        <div class="product-gallery">
            <img class="product-main-image" src="<?= e(app_url($mainImage)); ?>" alt="<?= e($product['title']); ?>">
            <div class="thumbnail-row">
                <?php foreach ($galleryImages as $image): ?>
                    <img src="<?= e(app_url($image)); ?>" alt="<?= e($product['title']); ?>">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-summary">
            <span class="product-brand"><?= e($product['brand']); ?></span>
            <h1><?= e($product['title']); ?></h1>
            <p class="product-price"><?= e(format_price((float) $product['sell_price'])); ?></p>
            <p class="product-rating"><?= e(rating_stars((int) $product['condition_rating'])); ?> <span><?= e(condition_label((int) $product['condition_rating'])); ?> condition</span></p>
            <ul class="detail-list">
                <li><strong>Category:</strong> <?= e($product['category']); ?></li>
                <li><strong>Gender:</strong> <?= e($product['gender']); ?></li>
                <li><strong>Size:</strong> <?= e($product['size_label']); ?></li>
                <li><strong>Seller:</strong> <?= e($product['seller_name'] ?? 'Pastimes Seller'); ?></li>
                <li><strong>Stock:</strong> <?= e((string) ($product['inventory_quantity'] ?? 1)); ?></li>
            </ul>
            <p class="product-description"><?= e($product['description']); ?></p>
            <form method="post" class="stacked-form">
                <button class="button button-dark button-block" type="submit" data-sell-price="<?= e(number_format((float) $product['sell_price'], 2)); ?>">Add To Cart</button>
            </form>

            <div class="message-box">
                <h2>Need More Detail?</h2>
                <p class="muted">Use Messages after login to ask sellers about fit, delivery, or condition before purchasing.</p>
                <?php if (!empty($product['seller_id'])): ?>
                    <a class="button button-sand" href="<?= e(app_url('messages.php?seller_id=' . (int) $product['seller_id'] . '&product_id=' . $productId)); ?>">Message Seller</a>
                <?php endif; ?>
                <a class="button button-light" href="<?= e(app_url('messages.php')); ?>">Open Messages</a>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | Cart';

if (is_post()) {
    Cart::ProcessInput($_POST);
    set_flash('Cart updated.', 'success');
    redirect_to('cart.php');
}

$cartData = cart_details();
require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container page-title-block">
        <div>
            <h1>Cart</h1>
            <p>Review and modify the items you've selected.</p>
        </div>
    </div>
    <div class="container cart-layout">
        <section class="dashboard-card">
            <?php if ($cartData['items']): ?>
                <table class="cart-table">
                    <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartData['items'] as $item): ?>
                        <tr>
                            <td class="cart-product">
                                <img src="<?= e(app_url(product_image_path($item['product']))); ?>" alt="<?= e($item['product']['title']); ?>">
                                <div>
                                    <strong><?= e($item['product']['title']); ?></strong>
                                    <p><?= e($item['product']['brand']); ?> | Size <?= e($item['product']['size_label']); ?></p>
                                </div>
                            </td>
                            <td><?= e(format_price((float) $item['product']['sell_price'])); ?></td>
                            <td>
                                <form method="post" class="table-actions">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="clothes_id" value="<?= e((string) $item['product']['clothes_id']); ?>">
                                    <input type="number" name="quantity" min="0" value="<?= e((string) $item['quantity']); ?>" style="max-width: 80px; margin: 0;">
                                    <button class="button button-dark" type="submit">Save</button>
                                </form>
                            </td>
                            <td><?= e(format_price((float) $item['subtotal'])); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="clothes_id" value="<?= e((string) $item['product']['clothes_id']); ?>">
                                    <button type="submit" class="button button-light">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">Your cart is empty.</div>
            <?php endif; ?>
        </section>
            <aside class="dashboard-card summary-box">
                <h2>Cart Summary</h2>
                <p class="summary-line"><span>Subtotal</span><strong><?= e(format_price((float) $cartData['total'])); ?></strong></p>
                <p class="summary-line"><span>Delivery Fee</span><strong>FREE</strong></p>
                <hr>
                <p class="summary-line"><span>Total</span><strong><?= e(format_price((float) $cartData['total'])); ?></strong></p>
                <div class="button-stack">
                    <a class="button button-sand button-block" href="<?= e(app_url('checkout.php')); ?>">Proceed to Checkout</a>
                    <a class="button button-light button-block" href="<?= e(app_url('shop.php')); ?>">Continue Shopping</a>
                    <form method="post">
                        <input type="hidden" name="action" value="empty">
                        <button class="button button-outline button-block" type="submit">Empty Cart</button>
                    </form>
                </div>
            </aside>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

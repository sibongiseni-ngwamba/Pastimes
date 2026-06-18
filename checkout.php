<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_user_login();
$pageTitle = 'Pastimes | Checkout';
$cartData = cart_details();
$addresses = db_all('SELECT * FROM tblAddress WHERE user_id = ? ORDER BY is_default DESC, created_at DESC', 'i', [(int) $user['user_id']]);

if ($cartData['items'] === []) {
    set_flash('Add an item to the cart before checking out.', 'error');
    redirect_to('shop.php');
}

if (is_post()) {
    try {
        $checkout = Cart::Checkout((int) ($_POST['address_id'] ?? 0));
        $_SESSION['last_checkout'] = $checkout;
        unset($_SESSION['admin_id'], $_SESSION['username'], $_SESSION['role'], $_SESSION['is_seller']);
        Auth::logoutUser();
        set_flash(
            'Your order has been placed successfully. Please sign in again whenever you are ready to continue shopping.',
            'success'
        );
        redirect_to('login.php');
    } catch (Throwable $exception) {
        set_flash('Checkout failed: ' . $exception->getMessage(), 'error');
        redirect_to('checkout.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container split-layout">
        <div class="content-main">
            <h1>Checkout</h1>
            <p class="muted">Choose a saved delivery address and confirm the order.</p>
            <?php if (isset($_SESSION['last_checkout'])): ?>
                <div class="inline-alert inline-alert-success">
                    <strong>Your order has been placed successfully.</strong><br>
                    Order Number: <?= e((string) ($_SESSION['last_checkout']['order_reference'] ?? '')); ?><br>
                    Session ID: <?= e((string) ($_SESSION['last_checkout']['session_reference'] ?? '')); ?>
                </div>
                <?php unset($_SESSION['last_checkout']); ?>
            <?php endif; ?>
            <?php if ($addresses): ?>
                <form method="post" class="form-card">
                    <label>Select Address</label>
                    <select name="address_id" required>
                        <?php foreach ($addresses as $address): ?>
                            <option value="<?= e((string) $address['address_id']); ?>"><?= e($address['address_label'] . ' - ' . $address['street_address'] . ', ' . $address['city']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Confirm Order">
                </form>
            <?php else: ?>
                <div class="empty-state">You need to save a delivery address first. <a href="<?= e(app_url('addresses.php')); ?>">Add one now</a>.</div>
            <?php endif; ?>
        </div>
        <aside class="content-side">
            <div class="summary-card checkout-summary">
                <h3>Order Summary</h3>
                <div class="order-summary-list">
                <?php foreach ($cartData['items'] as $item): ?>
                    <div class="order-summary-row">
                        <span class="order-summary-name"><?= e($item['product']['title']); ?> x <?= e((string) $item['quantity']); ?></span>
                        <strong class="order-summary-price">R<?= e(number_format((float) $item['subtotal'], 2)); ?></strong>
                    </div>
                <?php endforeach; ?>
                </div>
                <hr>
                <div class="order-summary-row order-summary-total">
                    <span class="order-summary-name">Total</span>
                    <strong class="order-summary-price">R<?= e(number_format((float) $cartData['total'], 2)); ?></strong>
                </div>
            </div>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

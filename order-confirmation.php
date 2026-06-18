<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | Order Confirmation';
$orderId = (int) ($_GET['id'] ?? 0);
$user = current_user();
$order = null;
if ($user && $orderId > 0) {
    $order = db_one('SELECT * FROM tblOrder WHERE order_id = ? AND user_id = ?', 'ii', [$orderId, (int) $user['user_id']]);
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Order Confirmed</h1>
            <?php if ($order): ?>
                <p>Your order number is <strong><?= e((string) ($order['order_reference'] ?? ('ORD' . date('Y') . str_pad((string) ($order['order_id'] ?? 0), 5, '0', STR_PAD_LEFT)))); ?></strong>.</p>
                <p>Session ID: <strong><?= e((string) ($order['session_reference'] ?? '')); ?></strong></p>
                <?php if (isset($order['order_total'])): ?>
                    <p>Total: <strong><?= e(format_price((float) $order['order_total'])); ?></strong></p>
                <?php endif; ?>
                <?php if (isset($order['status'])): ?>
                    <p>Status: <?= status_badge((string) $order['status']); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="muted">Order not found.</p>
            <?php endif; ?>
            <div class="button-stack" style="margin-top: 18px;">
                <a class="button button-sand button-block" href="<?= e(app_url('shop.php')); ?>">Continue Shopping</a>
                <a class="button button-light button-block" href="<?= e(app_url('index.php')); ?>">Home</a>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

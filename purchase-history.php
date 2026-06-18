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
$pageTitle = 'Pastimes | Purchase History';

$orders = db_all(
    'SELECT * FROM tblOrder WHERE user_id = ? ORDER BY created_at DESC',
    'i',
    [(int) $user['user_id']]
);

$grandTotal = 0.0;

foreach ($orders as $order) {
    $grandTotal += (float) $order['order_total'];
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container page-title-block">
        <div>
            <h1>Purchase History</h1>
            <p>Every customer purchase with item-level detail and a running grand total.</p>
        </div>
    </div>

    <div class="container purchase-history">
        <?php if ($orders): ?>
            <?php foreach ($orders as $order): ?>
                <?php $items = db_all('SELECT oi.*, c.title, c.brand FROM tblOrderItem oi JOIN tblClothes c ON c.clothes_id = oi.clothes_id WHERE oi.order_id = ?', 'i', [(int) $order['order_id']]); ?>
                <article class="dashboard-card">
                    <div class="message-header">
                        <strong><?= e($order['order_reference'] ?: generate_order_reference_number((int) $order['order_id'])); ?></strong>
                        <span><?= e($order['created_at']); ?></span>
                    </div>
                    <p>
                        Status: <?= status_badge($order['status']); ?>
                        | Total: <strong><?= e(format_price((float) $order['order_total'])); ?></strong>
                    </p>
                    <table class="info-table">
                        <thead>
                        <tr><th>Product</th><th>Quantity</th><th>Price Each</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['title']); ?> <span class="muted">(<?= e($item['brand']); ?>)</span></td>
                                <td><?= e((string) $item['quantity']); ?></td>
                                <td><?= e(format_price((float) $item['price_each'])); ?></td>
                                <td><?= e(format_price((float) $item['price_each'] * (int) $item['quantity'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </article>
            <?php endforeach; ?>

            <div class="dashboard-card purchase-total">
                <strong>Grand Total of All Purchases</strong>
                <span><?= e(format_price($grandTotal)); ?></span>
            </div>
        <?php else: ?>
            <div class="empty-state">No purchases found yet.</div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

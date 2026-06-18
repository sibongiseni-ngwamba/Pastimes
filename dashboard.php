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
$pageTitle = 'Pastimes | Dashboard';

$orderCount = db_one('SELECT COUNT(*) AS total FROM tblOrder WHERE user_id = ?', 'i', [(int) $user['user_id']]);
$addressCount = db_one('SELECT COUNT(*) AS total FROM tblAddress WHERE user_id = ?', 'i', [(int) $user['user_id']]);
$messageCount = db_one('SELECT COUNT(*) AS total FROM tblMessage WHERE receiver_user_id = ? OR sender_user_id = ? OR is_broadcast = 1', 'ii', [(int) $user['user_id'], (int) $user['user_id']]);

require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container dashboard-head">
        <div>
            <h1>User <?= e($user['full_name']); ?> is logged in</h1>
            <p>Customer status: <?= status_badge($user['customer_status']); ?> Seller status: <?= status_badge($user['seller_status']); ?></p>
        </div>
        <div class="dashboard-actions">
            <a class="button button-sand" href="<?= e(app_url('shop.php')); ?>">Continue Shopping</a>
            <a class="button button-light" href="<?= e(app_url('request-seller.php')); ?>">Sell An Item</a>
        </div>
    </div>

    <div class="container summary-grid">
        <article class="summary-card"><span>Orders</span><strong><?= e((string) ($orderCount['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Addresses</span><strong><?= e((string) ($addressCount['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Messages</span><strong><?= e((string) ($messageCount['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Cart</span><strong><?= e((string) cart_count()); ?></strong></article>
    </div>

    <div class="container dashboard-grid">
        <a class="dashboard-card" href="<?= e(app_url('profile.php')); ?>"><h2>Profile</h2><p>Update your customer information.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('addresses.php')); ?>"><h2>Addresses</h2><p>Save delivery details for checkout.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('purchase-history.php')); ?>"><h2>Purchase History</h2><p>Review previous purchases and totals.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('orders.php')); ?>"><h2>Orders</h2><p>Review order status and shipping updates.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('messages.php')); ?>"><h2>Messages</h2><p>View seller conversations and admin notices.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('request-seller.php')); ?>"><h2>Seller Application</h2><p>Request access to upload clothes for sale.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('seller/dashboard.php')); ?>"><h2>Seller Dashboard</h2><p>Open seller tools if your application is approved.</p></a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

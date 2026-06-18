<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
$admin = require_admin_login();
$pageTitle = 'Pastimes | Admin Order Detail';
$order = db_one('SELECT o.*, u.full_name, a.street_address, a.city, a.province FROM tblOrder o JOIN tblUser u ON u.user_id = o.user_id JOIN tblAddress a ON a.address_id = o.address_id WHERE o.order_id = ?', 'i', [(int) ($_GET['id'] ?? 0)]);
$items = $order ? db_all('SELECT oi.*, c.title FROM tblOrderItem oi JOIN tblClothes c ON c.clothes_id = oi.clothes_id WHERE oi.order_id = ?', 'i', [(int) $order['order_id']]) : [];
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <?php if ($order): ?>
            <h1>Order <?= e($order['order_reference'] ?: generate_order_reference_number((int) $order['order_id'])); ?></h1>
            <p>Customer: <?= e($order['full_name']); ?></p>
            <p>Delivery: <?= e($order['street_address'] . ', ' . $order['city'] . ', ' . $order['province']); ?></p>
            <p>Session ID: <?= e($order['session_reference'] ?? 'N/A'); ?></p>
            <table>
                <thead><tr><th>Item</th><th>Quantity</th><th>Price</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title']); ?></td>
                        <td><?= e((string) $item['quantity']); ?></td>
                        <td>R<?= e(number_format((float) $item['price_each'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">Order not found.</div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

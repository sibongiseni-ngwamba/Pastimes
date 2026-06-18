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
$pageTitle = 'Pastimes | Order Detail';
$order = db_one('SELECT * FROM tblOrder WHERE order_id = ? AND user_id = ?', 'ii', [(int) ($_GET['id'] ?? 0), (int) $user['user_id']]);
$items = $order ? db_all('SELECT oi.*, c.title FROM tblOrderItem oi JOIN tblClothes c ON c.clothes_id = oi.clothes_id WHERE oi.order_id = ?', 'i', [(int) $order['order_id']]) : [];
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <?php if ($order): ?>
            <h1>Order <?= e($order['order_reference'] ?: generate_order_reference_number((int) $order['order_id'])); ?></h1>
            <p><?= status_badge($order['status']); ?> | Total: R<?= e(number_format((float) $order['order_total'], 2)); ?></p>
            <table>
                <thead><tr><th>Item</th><th>Quantity</th><th>Price Each</th></tr></thead>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>

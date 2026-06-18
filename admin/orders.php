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
$pageTitle = 'Pastimes | Admin Orders';

if (is_post()) {
    execute_sql('UPDATE tblOrder SET status = ? WHERE order_id = ?', 'si', [$_POST['status'], (int) $_POST['order_id']]);
    set_flash('Order status updated.', 'success');
    redirect_to('admin/orders.php');
}

$orders = db_all('SELECT o.*, u.full_name FROM tblOrder o JOIN tblUser u ON u.user_id = o.user_id ORDER BY o.created_at DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>Orders</h1>
        <table>
            <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order['order_reference'] ?: generate_order_reference_number((int) $order['order_id'])); ?></td>
                    <td><?= e($order['full_name']); ?></td>
                    <td>R<?= e(number_format((float) $order['order_total'], 2)); ?></td>
                    <td><?= status_badge($order['status']); ?></td>
                    <td>
                        <form method="post" class="table-actions">
                            <input type="hidden" name="order_id" value="<?= e((string) $order['order_id']); ?>">
                            <select name="status" style="margin:0;">
                                <?php foreach (['pending', 'dispatched', 'delivered', 'cancelled'] as $status): ?>
                                    <option value="<?= e($status); ?>" <?= $order['status'] === $status ? 'selected' : ''; ?>><?= e(ucfirst($status)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Save</button>
                            <a href="<?= e(app_url('admin/order-detail.php?id=' . $order['order_id'])); ?>">View</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

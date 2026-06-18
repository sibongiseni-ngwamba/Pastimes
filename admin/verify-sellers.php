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
$pageTitle = 'Pastimes | Verify Sellers';

if (isset($_GET['approve'])) {
    $applicationId = (int) $_GET['approve'];
    $application = db_one('SELECT * FROM tblSellerApplication WHERE application_id = ?', 'i', [$applicationId]);
    if ($application) {
        execute_sql('UPDATE tblSellerApplication SET status = "approved", admin_id = ?, reviewed_at = NOW() WHERE application_id = ?', 'ii', [(int) $admin['admin_id'], $applicationId]);
        execute_sql('UPDATE tblUser SET seller_status = "approved", role = "seller" WHERE user_id = ?', 'i', [(int) $application['user_id']]);
        execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, "Seller application approved", "Your seller request has been approved. You can now submit listings.", 0, 0)', 'ii', [(int) $admin['admin_id'], (int) $application['user_id']]);
        set_flash('Seller approved successfully.', 'success');
    }
    redirect_to('admin/verify-sellers.php');
}

if (isset($_GET['reject'])) {
    $applicationId = (int) $_GET['reject'];
    $application = db_one('SELECT * FROM tblSellerApplication WHERE application_id = ?', 'i', [$applicationId]);
    if ($application) {
        execute_sql('UPDATE tblSellerApplication SET status = "rejected", admin_id = ?, reviewed_at = NOW() WHERE application_id = ?', 'ii', [(int) $admin['admin_id'], $applicationId]);
        execute_sql('UPDATE tblUser SET seller_status = "rejected" WHERE user_id = ?', 'i', [(int) $application['user_id']]);
        execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, "Seller application rejected", "Your seller request was not approved at this time. Please review the requirements and reapply later.", 0, 0)', 'ii', [(int) $admin['admin_id'], (int) $application['user_id']]);
        set_flash('Seller application rejected.', 'success');
    }
    redirect_to('admin/verify-sellers.php');
}

$applications = db_all('SELECT sa.*, u.full_name, u.email FROM tblSellerApplication sa JOIN tblUser u ON u.user_id = sa.user_id ORDER BY sa.created_at DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>Seller Applications</h1>
        <table>
            <thead><tr><th>User</th><th>ID Number</th><th>Status</th><th>Motivation</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?= e($application['full_name']); ?><br><span class="muted"><?= e($application['email']); ?></span></td>
                    <td><?= e($application['id_number']); ?></td>
                    <td><?= status_badge($application['status']); ?></td>
                    <td><?= e($application['motivation']); ?></td>
                    <td>
                        <a href="<?= e(app_url('admin/verify-sellers.php?approve=' . $application['application_id'])); ?>">Approve</a> |
                        <a href="<?= e(app_url('admin/verify-sellers.php?reject=' . $application['application_id'])); ?>">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

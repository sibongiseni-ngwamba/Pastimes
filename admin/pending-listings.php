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
$pageTitle = 'Pastimes | Pending Listings';

if (isset($_GET['approve'])) {
    $listing = db_one('SELECT * FROM tblClothes WHERE clothes_id = ?', 'i', [(int) $_GET['approve']]);
    execute_sql('UPDATE tblClothes SET status = "approved" WHERE clothes_id = ?', 'i', [(int) $_GET['approve']]);
    if ($listing && !empty($listing['seller_id'])) {
        execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, "Listing approved", "Your listing has been approved and is now visible in the shop.", 0, 0)', 'ii', [(int) $admin['admin_id'], (int) $listing['seller_id']]);
    }
    set_flash('Listing approved.', 'success');
    redirect_to('admin/pending-listings.php');
}

if (isset($_GET['reject'])) {
    $listing = db_one('SELECT * FROM tblClothes WHERE clothes_id = ?', 'i', [(int) $_GET['reject']]);
    execute_sql('UPDATE tblClothes SET status = "rejected" WHERE clothes_id = ?', 'i', [(int) $_GET['reject']]);
    if ($listing && !empty($listing['seller_id'])) {
        execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, "Listing rejected", "Your listing was rejected. Please review the condition, category, or image quality and resubmit.", 0, 0)', 'ii', [(int) $admin['admin_id'], (int) $listing['seller_id']]);
    }
    set_flash('Listing rejected.', 'success');
    redirect_to('admin/pending-listings.php');
}

$pendingListings = db_all("SELECT c.*, u.full_name AS seller_name FROM tblClothes c LEFT JOIN tblUser u ON u.user_id = c.seller_id WHERE c.status = 'pending' ORDER BY c.created_at DESC");
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>Pending Listings</h1>
        <?php if ($pendingListings): ?>
            <table>
                <thead><tr><th>Item</th><th>Seller</th><th>Price</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($pendingListings as $listing): ?>
                    <tr>
                        <td><?= e($listing['title']); ?><br><span class="muted"><?= e($listing['description']); ?></span></td>
                        <td><?= e($listing['seller_name'] ?? 'Unknown'); ?></td>
                        <td>R<?= e(number_format((float) $listing['sell_price'], 2)); ?></td>
                        <td>
                            <a href="<?= e(app_url('admin/pending-listings.php?approve=' . $listing['clothes_id'])); ?>">Approve</a> |
                            <a href="<?= e(app_url('admin/pending-listings.php?reject=' . $listing['clothes_id'])); ?>">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">No pending listings.</div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

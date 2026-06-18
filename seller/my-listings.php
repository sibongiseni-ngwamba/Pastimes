<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_seller_login();
$pageTitle = 'Pastimes | My Listings';
$listings = db_all('SELECT * FROM tblClothes WHERE seller_id = ? ORDER BY created_at DESC', 'i', [(int) $user['user_id']]);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>My Listings</h1>
        <?php if ($listings): ?>
            <table>
                <thead><tr><th>Title</th><th>Price</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><?= e($listing['title']); ?></td>
                        <td>R<?= e(number_format((float) $listing['sell_price'], 2)); ?></td>
                        <td><?= status_badge($listing['status']); ?></td>
                        <td><a href="<?= e(app_url('seller/edit-listing.php?id=' . $listing['clothes_id'])); ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">No listings submitted yet.</div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

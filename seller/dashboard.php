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
$pageTitle = 'Pastimes | Seller Dashboard';
$activeCount = db_one("SELECT COUNT(*) AS total FROM tblClothes WHERE seller_id = ? AND status = 'approved'", 'i', [(int) $user['user_id']]);
$soldCount = db_one("SELECT COUNT(*) AS total FROM tblClothes WHERE seller_id = ? AND status = 'sold'", 'i', [(int) $user['user_id']]);
$pendingCount = db_one("SELECT COUNT(*) AS total FROM tblSellerApplication WHERE user_id = ? AND status = 'pending'", 'i', [(int) $user['user_id']]);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>Seller Dashboard</h1>
        <div class="dashboard-grid">
            <div class="metric-card"><h3><?= e((string) ($activeCount['total'] ?? 0)); ?></h3><p>Active Listings</p></div>
            <div class="metric-card"><h3><?= e((string) ($soldCount['total'] ?? 0)); ?></h3><p>Sold Items</p></div>
            <div class="metric-card"><h3><?= e((string) ($pendingCount['total'] ?? 0)); ?></h3><p>Pending Seller Requests</p></div>
        </div>
        <div class="summary-grid" style="margin-top: 2rem;">
            <a class="metric-card" href="<?= e(app_url('seller/sell-item.php')); ?>"><h3>Sell Item</h3><p>Create a new clothing listing for admin approval.</p></a>
            <a class="metric-card" href="<?= e(app_url('seller/my-listings.php')); ?>"><h3>My Listings</h3><p>View and edit approved product listings.</p></a>
            <a class="metric-card" href="<?= e(app_url('seller/sold-items.php')); ?>"><h3>Sold Items</h3><p>Track products already purchased.</p></a>
            <a class="metric-card" href="<?= e(app_url('seller/seller-profile.php')); ?>"><h3>Seller Profile</h3><p>Review your seller account details.</p></a>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

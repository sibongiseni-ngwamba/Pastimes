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
$pageTitle = 'Pastimes | Admin Panel';
$pendingCustomers = db_one("SELECT COUNT(*) AS total FROM tblUser WHERE customer_status = 'pending' AND role <> 'admin'", '', []);
$pendingSellers = db_one("SELECT COUNT(*) AS total FROM tblSellerApplication WHERE status = 'pending'", '', []);
$pendingListings = db_one("SELECT COUNT(*) AS total FROM tblClothes WHERE status = 'pending'", '', []);
$messageCount = db_one("SELECT COUNT(*) AS total FROM tblMessage", '', []);
$orderCount = db_one("SELECT COUNT(*) AS total FROM tblOrder", '', []);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section section-tight">
    <div class="container dashboard-head">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Signed in as <?= e($admin['full_name']); ?>. Verify customers, manage users, and approve listings.</p>
        </div>
        <div class="dashboard-actions">
            <a class="button button-light" href="<?= e(app_url('admin/users.php')); ?>">Manage Customers</a>
            <a class="button button-sand" href="<?= e(app_url('admin/pending-listings.php')); ?>">Approve Listings</a>
        </div>
    </div>

    <div class="container summary-grid">
        <article class="summary-card"><span>Pending Customers</span><strong><?= e((string) ($pendingCustomers['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Seller Applications</span><strong><?= e((string) ($pendingSellers['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Pending Listings</span><strong><?= e((string) ($pendingListings['total'] ?? 0)); ?></strong></article>
        <article class="summary-card"><span>Orders</span><strong><?= e((string) ($orderCount['total'] ?? 0)); ?></strong></article>
    </div>

    <div class="container dashboard-grid">
        <a class="dashboard-card" href="<?= e(app_url('admin/users.php')); ?>"><h2>Users</h2><p>Verify, add, update, and delete customer records.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/verify-sellers.php')); ?>"><h2>Verify Sellers</h2><p>Approve or reject seller applications.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/pending-listings.php')); ?>"><h2>Pending Listings</h2><p>Approve item submissions.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/manage-listings.php')); ?>"><h2>Manage Listings</h2><p>Review all clothes currently in the system.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/orders.php')); ?>"><h2>Orders</h2><p>Update delivery and order statuses.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/messages.php')); ?>"><h2>Messages</h2><p>Communicate with buyers and sellers.</p></a>
        <a class="dashboard-card" href="<?= e(app_url('admin/broadcast.php')); ?>"><h2>Broadcast</h2><p>Send announcements to every user.</p></a>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

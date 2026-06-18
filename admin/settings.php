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
$pageTitle = 'Pastimes | Settings';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Settings</h1>
            <p class="muted">This placeholder page completes the admin sitemap and can be used in your video to show the full prototype navigation.</p>
            <label>Marketplace Mode</label>
            <input value="Managed marketplace with admin verification" readonly>
            <label>Database</label>
            <input value="ClothingStore" readonly>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

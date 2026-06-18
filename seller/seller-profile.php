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
$pageTitle = 'Pastimes | Seller Profile';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Seller Profile</h1>
            <p><strong>Seller:</strong> <?= e($user['full_name']); ?></p>
            <p><strong>Username:</strong> <?= e($user['username']); ?></p>
            <p><strong>Email:</strong> <?= e($user['email']); ?></p>
            <p><strong>Seller Status:</strong> <?= status_badge($user['seller_status']); ?></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | Reset Password';
$token = $_GET['token'] ?? 'demo-token';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Reset Password</h1>
            <p class="muted">Demo token: <?= e($token); ?></p>
            <form>
                <label>New Password</label>
                <input type="password" disabled>
                <label>Confirm Password</label>
                <input type="password" disabled>
                <input type="submit" value="Reset Password" disabled>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | Forgot Password';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Forgot Password</h1>
            <p class="muted">Prototype page: in a future version this would generate a secure reset token and email workflow.</p>
            <form>
                <label>Email Address</label>
                <input type="email" placeholder="your@email.com">
                <input type="submit" value="Request Reset" disabled>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

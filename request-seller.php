<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_user_login();
$pageTitle = 'Pastimes | Seller Application';
$errors = [];
$existingApplication = db_one('SELECT * FROM tblSellerApplication WHERE user_id = ? ORDER BY created_at DESC LIMIT 1', 'i', [(int) $user['user_id']]);
$canSubmit = !$existingApplication || ($existingApplication['status'] ?? '') === 'rejected';

if (is_post() && $canSubmit) {
    $idNumber = trim($_POST['id_number'] ?? '');
    $motivation = trim($_POST['motivation'] ?? '');

    if ($idNumber === '' || $motivation === '') {
        $errors[] = 'ID number and motivation are required.';
    } else {
        execute_sql('INSERT INTO tblSellerApplication (user_id, id_number, motivation, status) VALUES (?, ?, ?, "pending")', 'iss', [(int) $user['user_id'], $idNumber, $motivation]);
        execute_sql('UPDATE tblUser SET seller_status = "pending" WHERE user_id = ?', 'i', [(int) $user['user_id']]);
        set_flash('Seller application submitted for admin review.', 'success');
        redirect_to('request-seller.php');
    }
}

$existingApplication = db_one('SELECT * FROM tblSellerApplication WHERE user_id = ? ORDER BY created_at DESC LIMIT 1', 'i', [(int) $user['user_id']]);
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-section">
    <div class="auth-card auth-card-wide">
        <h1>Request Seller Verification</h1>
        <p>Verified sellers can upload clothing listings for admin approval.</p>
        <?php if ($existingApplication && ($existingApplication['status'] ?? '') !== 'rejected'): ?>
            <div class="inline-alert inline-alert-success">
                Current application status: <?= status_badge($existingApplication['status']); ?><br>
                Submitted on <?= e($existingApplication['created_at']); ?>
            </div>
        <?php elseif ($existingApplication && ($existingApplication['status'] ?? '') === 'rejected'): ?>
            <div class="inline-alert">
                Your previous application was rejected. You may submit a new request below.
            </div>
        <?php endif; ?>
        <?php if ($canSubmit): ?>
            <?php foreach ($errors as $error): ?>
                <div class="inline-alert"><?= e($error); ?></div>
            <?php endforeach; ?>
            <form method="post">
                <label>South African ID Number *
                    <input name="id_number" value="<?= e(old('id_number')); ?>" required maxlength="13" placeholder="Enter your 13-digit ID">
                </label>
                <label>Why do you want to become a seller? *
                    <textarea name="motivation" required placeholder="Tell the admin team what you plan to sell."><?= e(old('motivation')); ?></textarea>
                </label>
                <button class="button button-dark button-block" type="submit">Submit Application</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
